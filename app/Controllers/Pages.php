<?php

namespace App\Controllers;

class Pages extends BaseStoreController
{
    public function about()
    {
        return $this->page('About Us', 'about-us', 'about');
    }

    public function contact()
    {
        return $this->storeView('contact', [
            'pageTitle'  => 'Contact Us',
            'activeMenu' => 'contact',
            'content'    => $this->getContent('contact-us'),
            'cssFile'    => 'demo22.min.css',
            'bodyClass'  => 'store-qist',
        ]);
    }

    public function faq()
    {
        return $this->storeView('page', [
            'pageTitle'  => 'FAQ',
            'activeMenu' => 'faq',
            'content'    => [
                'title' => 'Frequently Asked Questions',
                'body'  => '<h3>How do installments work?</h3><p>Choose a product, select a plan, and submit a booking request. Our team verifies details before approval.</p><h3>Is booking automatic approval?</h3><p>No. Submitting an order is a booking request only.</p><h3>What documents are needed?</h3><p>Valid CNIC and reachable phone number are typically required.</p>',
            ],
            'cssFile'    => 'demo22.min.css',
            'bodyClass'  => 'store-qist',
        ]);
    }

    public function privacy()
    {
        return $this->page('Privacy Policy', 'privacy-policy');
    }

    public function terms()
    {
        return $this->page('Terms & Conditions', 'terms-and-conditions');
    }

    public function installmentTerms()
    {
        return $this->page('Installment Policy', 'installment-terms');
    }

    public function returnPolicy()
    {
        return $this->page('Return and Refund Policy', 'return-policy');
    }

    public function deliveryPolicy()
    {
        return $this->page('Delivery Policy', 'delivery-policy');
    }

    public function paymentPolicy()
    {
        return $this->page('Payment Policy', 'payment-policy');
    }

    public function furniturePolicy()
    {
        return $this->page('Furniture Policy', 'furniture-policy');
    }

    private function page(string $title, string $slug, string $activeMenu = ''): string
    {
        return $this->storeView('page', [
            'pageTitle'  => $title,
            'activeMenu' => $activeMenu,
            'content'    => $this->getContent($slug) ?: [
                'title' => $title,
                'body'  => '<p>Content coming soon.</p>',
            ],
            'cssFile'    => 'demo22.min.css',
            'bodyClass'  => 'store-qist',
        ]);
    }
}
