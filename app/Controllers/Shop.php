<?php

namespace App\Controllers;

use App\Models\CategoryModel;
use CodeIgniter\Database\BaseBuilder;
use CodeIgniter\HTTP\ResponseInterface;

class Shop extends BaseStoreController
{
    private const PER_PAGE = 12;

    public function index()
    {
        $filters          = $this->parseFilters();
        $categoryModel    = model(CategoryModel::class);
        $activeCategory   = $this->resolveCategory($categoryModel, $filters);
        $categoryNotFound = $filters['category_slug'] !== '' && $activeCategory === null;

        $baseBuilder = $this->baseProductBuilder($categoryModel, $filters, $activeCategory, $categoryNotFound);
        $bounds      = $this->catalogPriceBounds($baseBuilder);

        $productBuilder = $this->productListBuilder($baseBuilder, $filters);
        $totalProducts  = (int) $productBuilder->countAllResults(false);
        $products       = $productBuilder->limit(self::PER_PAGE, 0)->get()->getResultArray();
        $products       = $this->enrichProducts($products);

        $searchCategory = $this->searchCategorySlug($categoryModel, $activeCategory);
        $loadedCount    = count($products);
        $pageTitle      = 'Shop';

        if ($filters['search'] !== '') {
            $pageTitle = 'Search: ' . $filters['search'];
        } elseif ($activeCategory) {
            $pageTitle = $activeCategory['name'];
        }

        return $this->storeView('shop', [
            'pageTitle'        => $pageTitle,
            'activeMenu'       => 'shop',
            'products'         => $products,
            'totalProducts'    => $totalProducts,
            'loadedCount'      => $loadedCount,
            'hasMoreProducts'  => $loadedCount < $totalProducts,
            'perPage'          => self::PER_PAGE,
            'search'           => $filters['search'],
            'sort'             => $filters['sort'],
            'activeCategory'   => $activeCategory,
            'searchCategory'   => $searchCategory,
            'categoryNotFound' => $categoryNotFound,
            'minPrice'         => $filters['min_price'],
            'maxPrice'         => $filters['max_price'],
            'catalogMin'       => $bounds['min'],
            'catalogMax'       => $bounds['max'],
            'bodyClass'        => 'store-qist',
            'cssFile'          => 'demo22.min.css',
        ]);
    }

    public function loadMore(): ResponseInterface
    {
        $filters        = $this->parseFilters();
        $page           = max(1, (int) $this->request->getGet('page'));
        $categoryModel  = model(CategoryModel::class);
        $activeCategory = $this->resolveCategory($categoryModel, $filters);

        if ($filters['category_slug'] !== '' && $activeCategory === null) {
            return $this->jsonSuccess('OK', [
                'html'    => '',
                'page'    => $page,
                'hasMore' => false,
                'total'   => 0,
                'loaded'  => 0,
            ]);
        }

        $baseBuilder    = $this->baseProductBuilder($categoryModel, $filters, $activeCategory, false);
        $productBuilder = $this->productListBuilder($baseBuilder, $filters);
        $totalProducts  = (int) $productBuilder->countAllResults(false);
        $offset         = ($page - 1) * self::PER_PAGE;

        if ($offset >= $totalProducts) {
            return $this->jsonSuccess('OK', [
                'html'    => '',
                'page'    => $page,
                'hasMore' => false,
                'total'   => $totalProducts,
                'loaded'  => $totalProducts,
            ]);
        }

        $products = $productBuilder->limit(self::PER_PAGE, $offset)->get()->getResultArray();
        $products = $this->enrichProducts($products);

        $html = '';
        foreach ($products as $product) {
            $html .= view('store/partials/product_card', ['product' => $product, 'style' => 'qist']);
        }

        $loadedCount = min($offset + count($products), $totalProducts);

        return $this->jsonSuccess('OK', [
            'html'    => $html,
            'page'    => $page,
            'hasMore' => $loadedCount < $totalProducts,
            'total'   => $totalProducts,
            'loaded'  => $loadedCount,
        ]);
    }

    private function parseFilters(): array
    {
        $minPriceRaw = trim((string) $this->request->getGet('min_price'));
        $maxPriceRaw = trim((string) $this->request->getGet('max_price'));
        $minPrice    = $minPriceRaw !== '' ? max(0, (float) $minPriceRaw) : null;
        $maxPrice    = $maxPriceRaw !== '' ? max(0, (float) $maxPriceRaw) : null;

        if ($minPrice !== null && $maxPrice !== null && $minPrice > $maxPrice) {
            [$minPrice, $maxPrice] = [$maxPrice, $minPrice];
        }

        return [
            'search'        => trim((string) $this->request->getGet('q')),
            'category_slug' => trim((string) $this->request->getGet('category')),
            'sort'          => (string) $this->request->getGet('sort'),
            'min_price'     => $minPrice,
            'max_price'     => $maxPrice,
        ];
    }

    private function resolveCategory(CategoryModel $categoryModel, array $filters): ?array
    {
        if ($filters['category_slug'] === '') {
            return null;
        }

        return $categoryModel->where('slug', $filters['category_slug'])->where('status', 1)->first();
    }

    private function baseProductBuilder(
        CategoryModel $categoryModel,
        array $filters,
        ?array $activeCategory,
        bool $categoryNotFound
    ): BaseBuilder {
        $builder = db_connect()->table('products p')
            ->join('categories c', 'c.id = p.category_id', 'left')
            ->where('p.status', 1)
            ->where('p.deleted_at', null);

        if ($filters['category_slug'] !== '') {
            if ($activeCategory) {
                $ids = $categoryModel->idsWithChildren((int) $activeCategory['id']);
                $builder->whereIn('p.category_id', $ids);
            } elseif ($categoryNotFound) {
                $builder->where('p.id', 0);
            }
        }

        if ($filters['search'] !== '') {
            $builder->groupStart()
                ->like('p.name', $filters['search'])
                ->orLike('p.sku', $filters['search'])
                ->orLike('p.description', $filters['search'])
                ->orLike('c.name', $filters['search'])
                ->groupEnd();
        }

        return $builder;
    }

    private function catalogPriceBounds(BaseBuilder $baseBuilder): array
    {
        $boundsRow = (clone $baseBuilder)
            ->select('MIN(p.price) as catalog_min, MAX(p.price) as catalog_max', false)
            ->get()
            ->getRowArray();

        $catalogMin = (float) ($boundsRow['catalog_min'] ?? 0);
        $catalogMax = (float) ($boundsRow['catalog_max'] ?? 0);

        if ($catalogMax <= 0) {
            $catalogMax = 100000;
        }

        return [
            'min' => (int) floor($catalogMin),
            'max' => (int) ceil($catalogMax),
        ];
    }

    private function productListBuilder(BaseBuilder $baseBuilder, array $filters): BaseBuilder
    {
        $builder = (clone $baseBuilder)->select('p.*, c.name as category_name, c.slug as category_slug');

        if ($filters['min_price'] !== null) {
            $builder->where('p.price >=', $filters['min_price']);
        }
        if ($filters['max_price'] !== null) {
            $builder->where('p.price <=', $filters['max_price']);
        }

        switch ($filters['sort']) {
            case 'price_asc':
                $builder->orderBy('p.price', 'ASC');
                break;
            case 'price_desc':
                $builder->orderBy('p.price', 'DESC');
                break;
            case 'name':
                $builder->orderBy('p.name', 'ASC');
                break;
            default:
                $builder->orderBy('p.id', 'DESC');
        }

        return $builder;
    }

    private function searchCategorySlug(CategoryModel $categoryModel, ?array $activeCategory): string
    {
        if (! $activeCategory) {
            return '';
        }

        if (! empty($activeCategory['parent_id'])) {
            $parent = $categoryModel->find((int) $activeCategory['parent_id']);

            return $parent['slug'] ?? '';
        }

        return $activeCategory['slug'];
    }
}
