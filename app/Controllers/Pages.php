<?php

namespace App\Controllers;

class Pages extends BaseStoreController
{
    public function about()
    {
        return $this->storeView('page', [
            'pageTitle'  => 'About Us',
            'activeMenu' => 'about',
            'content'    => $this->getContent('about-us'),
            'cssFile'    => 'style.min.css',
        ]);
    }

    public function contact()
    {
        return $this->storeView('contact', [
            'pageTitle'  => 'Contact Us',
            'activeMenu' => 'contact',
            'content'    => $this->getContent('contact-us'),
            'cssFile'    => 'style.min.css',
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
            'cssFile'    => 'style.min.css',
        ]);
    }

    public function privacy()
    {
        return $this->storeView('page', [
            'pageTitle' => 'Privacy Policy',
            'content'   => $this->getContent('privacy-policy'),
            'cssFile'   => 'style.min.css',
        ]);
    }

    public function terms()
    {
        return $this->storeView('page', [
            'pageTitle' => 'Terms & Conditions',
            'content'   => $this->getContent('terms-and-conditions'),
            'cssFile'   => 'style.min.css',
        ]);
    }

    public function installmentTerms()
    {
        return $this->storeView('page', [
            'pageTitle' => 'Installment Terms',
            'content'   => $this->getContent('installment-terms'),
            'cssFile'   => 'style.min.css',
        ]);
    }
}
