<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class DemoStoreSeeder extends Seeder
{
    public function run()
    {
        $now = date('Y-m-d H:i:s');

        // Clear demo catalog (keep admin users/roles)
        $this->db->table('product_installment_plans')->emptyTable();
        $this->db->table('order_items')->emptyTable();
        $this->db->table('orders')->emptyTable();
        $this->db->table('products')->emptyTable();
        $this->db->table('categories')->emptyTable();
        $this->db->table('installment_plans')->emptyTable();
        $this->db->table('banners')->emptyTable();
        $this->db->table('customers')->emptyTable();

        $plans = [
            [
                'name' => '3 Month Easy Plan',
                'down_payment' => 10000,
                'monthly_installment' => 8000,
                'months' => 3,
                'total_payable' => 34000,
                'processing_charges' => 0,
                'terms' => 'Subject to verification. Booking is not financing approval.',
                'status' => 1,
            ],
            [
                'name' => '6 Month Standard Plan',
                'down_payment' => 15000,
                'monthly_installment' => 7000,
                'months' => 6,
                'total_payable' => 58000,
                'processing_charges' => 1000,
                'terms' => 'CNIC and contact verification required.',
                'status' => 1,
            ],
            [
                'name' => '12 Month Comfort Plan',
                'down_payment' => 20000,
                'monthly_installment' => 5500,
                'months' => 12,
                'total_payable' => 88000,
                'processing_charges' => 2000,
                'terms' => 'Installments due on the same date each month.',
                'status' => 1,
            ],
            [
                'name' => '18 Month Premium Plan',
                'down_payment' => 25000,
                'monthly_installment' => 4800,
                'months' => 18,
                'total_payable' => 113400,
                'processing_charges' => 2000,
                'terms' => 'Available on selected products only.',
                'status' => 1,
            ],
        ];
        foreach ($plans as &$plan) {
            $plan['created_at'] = $now;
            $plan['updated_at'] = $now;
        }
        $this->db->table('installment_plans')->insertBatch($plans);
        $planIds = array_column($this->db->table('installment_plans')->get()->getResultArray(), 'id');

        $categories = [
            ['name' => 'Electronics', 'slug' => 'electronics', 'image' => 'theme/images/demos/demo22/categories/1.png', 'description' => 'TVs, mobiles, audio and gadgets.', 'sort_order' => 1],
            ['name' => 'Home Appliances', 'slug' => 'home-appliances', 'image' => 'theme/images/demos/demo22/categories/2.png', 'description' => 'Kitchen and home essentials.', 'sort_order' => 2],
            ['name' => 'Computers', 'slug' => 'computers', 'image' => 'theme/images/demos/demo22/categories/3.png', 'description' => 'Laptops, PCs and accessories.', 'sort_order' => 3],
            ['name' => 'Fashion', 'slug' => 'fashion', 'image' => 'theme/images/demos/demo22/categories/4.png', 'description' => 'Clothing and lifestyle products.', 'sort_order' => 4],
            ['name' => 'Beauty', 'slug' => 'beauty', 'image' => 'theme/images/demos/demo22/categories/5.png', 'description' => 'Beauty and personal care.', 'sort_order' => 5],
            ['name' => 'Furniture', 'slug' => 'furniture', 'image' => 'theme/images/demos/demo22/categories/6.png', 'description' => 'Home furniture and décor.', 'sort_order' => 6],
        ];
        foreach ($categories as &$cat) {
            $cat['parent_id'] = null;
            $cat['status'] = 1;
            $cat['meta_title'] = $cat['name'] . ' | Rafi & Sons';
            $cat['meta_description'] = $cat['description'];
            $cat['created_at'] = $now;
            $cat['updated_at'] = $now;
        }
        $this->db->table('categories')->insertBatch($categories);
        $categoryRows = $this->db->table('categories')->where('parent_id', null)->get()->getResultArray();
        $catMap = [];
        foreach ($categoryRows as $row) {
            $catMap[$row['slug']] = (int) $row['id'];
        }

        $subcategories = [
            ['Televisions', 'televisions', 'electronics', 1],
            ['Mobiles', 'mobiles', 'electronics', 2],
            ['Audio', 'audio', 'electronics', 3],
            ['Kitchen', 'kitchen', 'home-appliances', 1],
            ['Laundry', 'laundry', 'home-appliances', 2],
            ['Cooling', 'cooling', 'home-appliances', 3],
            ['Laptops', 'laptops', 'computers', 1],
            ['Accessories', 'computer-accessories', 'computers', 2],
            ['Men', 'men-fashion', 'fashion', 1],
            ['Women', 'women-fashion', 'fashion', 2],
            ['Fragrance', 'fragrance', 'beauty', 1],
            ['Skincare', 'skincare', 'beauty', 2],
            ['Living Room', 'living-room', 'furniture', 1],
            ['Office', 'office-furniture', 'furniture', 2],
        ];
        $subRows = [];
        foreach ($subcategories as $sub) {
            [$name, $slug, $parentSlug, $sort] = $sub;
            $subRows[] = [
                'parent_id'        => $catMap[$parentSlug],
                'name'             => $name,
                'slug'             => $slug,
                'image'            => null,
                'description'      => $name . ' under ' . $parentSlug,
                'status'           => 1,
                'sort_order'       => $sort,
                'meta_title'       => $name . ' | Rafi & Sons',
                'meta_description' => $name,
                'created_at'       => $now,
                'updated_at'       => $now,
            ];
        }
        $this->db->table('categories')->insertBatch($subRows);
        foreach ($this->db->table('categories')->where('parent_id !=', null)->get()->getResultArray() as $row) {
            $catMap[$row['slug']] = (int) $row['id'];
        }

        $products = [
            ['LED Smart TV 43"', 'televisions', 'TV-43', 85000, [1, 2], 'Full HD smart television with streaming apps.'],
            ['LED Smart TV 55"', 'televisions', 'TV-55', 145000, [3, 4], '4K UHD smart TV with vivid display.'],
            ['Android Smartphone X1', 'mobiles', 'PHN-X1', 52000, [5, 6], '6.5" display, 128GB storage, dual SIM.'],
            ['Wireless Earbuds Pro', 'audio', 'AUD-EB1', 8500, [7], 'Noise isolation with long battery life.'],
            ['Refrigerator 14 Cu Ft', 'kitchen', 'REF-14', 98000, [8, 9], 'Energy efficient frost-free refrigerator.'],
            ['Automatic Washing Machine', 'laundry', 'WM-7KG', 72000, [10, 11], '7kg fully automatic washer.'],
            ['Microwave Oven 25L', 'kitchen', 'MW-25', 28000, [12], 'Convection microwave for everyday cooking.'],
            ['Air Conditioner 1.5 Ton', 'cooling', 'AC-15', 125000, [13, 14], 'Inverter AC with fast cooling.'],
            ['Laptop Core i5 8GB', 'laptops', 'LAP-I5', 135000, [15, 16], '15.6" laptop for work and study.'],
            ['Laptop Core i7 16GB', 'laptops', 'LAP-I7', 195000, [17], 'High performance laptop for professionals.'],
            ['Wireless Mouse & Keyboard', 'computer-accessories', 'ACC-MK', 6500, [18], 'Comfortable wireless combo set.'],
            ['Gaming Headset', 'computer-accessories', 'ACC-HS', 12000, [19], 'Stereo gaming headset with mic.'],
            ['Men Casual Jacket', 'men-fashion', 'FAS-J1', 7500, [20], 'Stylish all-season casual jacket.'],
            ['Women Handbag', 'women-fashion', 'FAS-B1', 9200, [21], 'Premium synthetic leather handbag.'],
            ['Sports Cap Pack', 'men-fashion', 'FAS-C1', 2500, [22], 'Comfort fit sports cap.'],
            ['Perfume Gift Set', 'fragrance', 'BEA-P1', 6800, [23], 'Long lasting fragrance gift set.'],
            ['Skincare Combo', 'skincare', 'BEA-S1', 4500, [24], 'Daily skincare essentials combo.'],
            ['Office Study Chair', 'office-furniture', 'FUR-C1', 18500, [1, 3], 'Ergonomic chair for home office.'],
            ['Wooden Coffee Table', 'living-room', 'FUR-T1', 22000, [4, 5], 'Modern wooden coffee table.'],
            ['Storage Cabinet', 'living-room', 'FUR-S1', 31000, [6, 7], 'Multi-purpose home storage cabinet.'],
        ];

        $productRows = [];
        $i = 0;
        foreach ($products as $item) {
            [$name, $catSlug, $sku, $price, $imgs, $desc] = $item;
            if (! is_array($imgs)) {
                $imgs = [(int) $imgs];
            }
            $imagePaths = [];
            foreach ($imgs as $imgNo) {
                $imagePaths[] = 'theme/images/demos/demo22/products/' . $imgNo . '.jpg';
            }

            $productRows[] = [
                'category_id'           => $catMap[$catSlug] ?? null,
                'name'                  => $name,
                'slug'                  => url_title($name, '-', true),
                'sku'                   => $sku,
                'price'                 => $price,
                'images'                => json_encode($imagePaths),
                'description'           => $desc,
                'stock_status'          => 'in_stock',
                'installment_available' => 1,
                'status'                => 1,
                'meta_title'            => $name . ' | Rafi & Sons',
                'meta_description'      => $desc,
                'created_at'            => $now,
                'updated_at'            => $now,
            ];
            $i++;
        }
        $this->db->table('products')->insertBatch($productRows);

        $allProducts = $this->db->table('products')->get()->getResultArray();
        $pivot = [];
        foreach ($allProducts as $index => $product) {
            // Assign 2–3 plans per product
            $assigned = array_slice($planIds, $index % max(1, count($planIds) - 2), 3);
            if (count($assigned) < 2) {
                $assigned = array_slice($planIds, 0, min(3, count($planIds)));
            }
            foreach ($assigned as $planId) {
                $pivot[] = [
                    'product_id'          => (int) $product['id'],
                    'installment_plan_id' => (int) $planId,
                ];
            }
        }
        if ($pivot) {
            $this->db->table('product_installment_plans')->insertBatch($pivot);
        }

        $banners = [
            [
                'position'    => 'home_slider',
                'title'       => 'Camera, Lens and Tablet',
                'subtitle'    => 'Financing Offer',
                'description' => 'Discount',
                'badge_text'  => '40% OFF',
                'button_text' => 'Shop now',
                'image'       => 'theme/images/demos/demo22/slides/1.jpg',
                'bg_color'    => '#e8e8ea',
                'style'       => 'light',
                'link'        => '/shop?category=electronics',
                'sort_order'  => 1,
            ],
            [
                'position'    => 'home_slider',
                'title'       => 'Up to 70% Discount',
                'subtitle'    => 'Flash Sales',
                'description' => 'Extra Off Everything online',
                'badge_text'  => null,
                'button_text' => 'Shop now',
                'image'       => 'theme/images/demos/demo22/slides/2.jpg',
                'bg_color'    => '#7a7675',
                'style'       => 'dark',
                'link'        => '/shop',
                'sort_order'  => 2,
            ],
            [
                'position'    => 'home_side',
                'title'       => 'Portable Drone SD9',
                'subtitle'    => "Through \nRafi & Sons",
                'description' => null,
                'badge_text'  => 'Up to 70% Off',
                'button_text' => 'Buy Now',
                'image'       => 'theme/images/demos/demo22/banner/drone.png',
                'bg_color'    => null,
                'style'       => 'dark',
                'link'        => '/shop?category=electronics',
                'sort_order'  => 1,
            ],
            [
                'position'    => 'home_mid',
                'title'       => 'Easy Installment Plans',
                'subtitle'    => null,
                'description' => 'Choose a plan that fits your budget and submit a booking request.',
                'badge_text'  => null,
                'button_text' => 'Shop Now',
                'image'       => 'theme/images/demos/demo22/banner/1.jpg',
                'bg_color'    => '#3cbea4',
                'style'       => 'dark',
                'link'        => '/shop',
                'sort_order'  => 1,
            ],
            [
                'position'    => 'home_mid',
                'title'       => 'Ready-To-Ship Products',
                'subtitle'    => null,
                'description' => 'Browse our latest electronics, appliances, and more.',
                'badge_text'  => null,
                'button_text' => 'New Arrivals',
                'image'       => 'theme/images/demos/demo22/banner/2.jpg',
                'bg_color'    => '#444443',
                'style'       => 'dark',
                'link'        => '/shop',
                'sort_order'  => 2,
            ],
        ];
        foreach ($banners as &$banner) {
            $banner['status']     = 1;
            $banner['created_at'] = $now;
            $banner['updated_at'] = $now;
        }
        $this->db->table('banners')->insertBatch($banners);

        $this->db->table('settings')->where('key', 'site_name')->update(['value' => 'Rafi & Sons']);
        $this->db->table('settings')->where('key', 'contact_phone')->update(['value' => '0800-123-456']);
        $this->db->table('settings')->where('key', 'contact_address')->update(['value' => 'Main Boulevard, City, Pakistan']);
        $this->db->table('settings')->where('key', 'contact_email')->update(['value' => 'info@rafiandsonsnr.com']);

        $this->db->table('contents')->where('slug', 'about-us')->update([
            'body' => '<p>Rafi & Sons is a trusted retail brand offering quality products on flexible installment plans. Making Life Easier, Every Day.</p><p>Browse our catalog, choose a plan that fits your budget, and submit a booking request. Our team will contact you for verification.</p>',
            'updated_at' => $now,
        ]);
        $this->db->table('contents')->where('slug', 'homepage')->update([
            'body' => 'Shop quality products with easy installment plans.',
            'updated_at' => $now,
        ]);
        $this->db->table('contents')->where('slug', 'installment-terms')->update([
            'body' => '<p>Submitting an order is an installment booking request and does not mean automatic financing approval. Final approval is subject to verification by Rafi & Sons.</p>',
            'updated_at' => $now,
        ]);

        // Sample customers
        $this->db->table('customers')->insertBatch([
            ['name' => 'Ali Khan', 'email' => 'ali@example.com', 'phone' => '03001234567', 'city' => 'Lahore', 'status' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Sara Ahmed', 'email' => 'sara@example.com', 'phone' => '03007654321', 'city' => 'Karachi', 'status' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Usmar Farooq', 'email' => 'umar@example.com', 'phone' => '03111222333', 'city' => 'Islamabad', 'status' => 1, 'created_at' => $now, 'updated_at' => $now],
        ]);
    }
}
