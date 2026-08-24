<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Seeds / updates CMS pages adapted from Surmawala policy pages,
 * branded as Rafi & Sons (no images, no third-party bank/contact numbers).
 */
class SeedPolicyPagesContent extends Migration
{
    public function up()
    {
        $now = date('Y-m-d H:i:s');

        foreach ($this->pages() as $page) {
            $existing = $this->db->table('contents')->where('slug', $page['slug'])->get()->getFirstRow('array');
            $row = [
                'title'      => $page['title'],
                'body'       => $page['body'],
                'status'     => 1,
                'updated_at' => $now,
            ];

            if ($existing) {
                $this->db->table('contents')->where('id', $existing['id'])->update($row);
            } else {
                $row['slug']       = $page['slug'];
                $row['created_at'] = $now;
                $this->db->table('contents')->insert($row);
            }
        }
    }

    public function down()
    {
        // Keep content; do not wipe production pages on rollback.
    }

    /**
     * @return list<array{slug: string, title: string, body: string}>
     */
    private function pages(): array
    {
        return [
            [
                'slug'  => 'about-us',
                'title' => 'About Us',
                'body'  => $this->aboutUs(),
            ],
            [
                'slug'  => 'return-policy',
                'title' => 'Return and Refund Policy',
                'body'  => $this->returnPolicy(),
            ],
            [
                'slug'  => 'delivery-policy',
                'title' => 'Delivery Policy',
                'body'  => $this->deliveryPolicy(),
            ],
            [
                'slug'  => 'terms-and-conditions',
                'title' => 'Terms and Conditions',
                'body'  => $this->terms(),
            ],
            [
                'slug'  => 'privacy-policy',
                'title' => 'Privacy Policy',
                'body'  => $this->privacy(),
            ],
            [
                'slug'  => 'payment-policy',
                'title' => 'Payment Policy',
                'body'  => $this->paymentPolicy(),
            ],
            [
                'slug'  => 'installment-terms',
                'title' => 'Installment Policy',
                'body'  => $this->installmentPolicy(),
            ],
            [
                'slug'  => 'furniture-policy',
                'title' => 'Furniture Policy',
                'body'  => $this->furniturePolicy(),
            ],
        ];
    }

    private function aboutUs(): string
    {
        return <<<'HTML'
<p>Rafi &amp; Sons is your trusted destination to shop bikes, electronic appliances, home essentials, and other consumer goods on flexible installment plans. Our focus on reliable service, fair dealing, and customer support helps us serve families across the region with quality products they can trust.</p>
<p><strong>Read on to learn more about Rafi &amp; Sons and our journey.</strong></p>

<h3>Our Journey</h3>
<p>Rafi &amp; Sons has been providing electronic goods, home appliances, bikes, and other products from leading brands under one roof. We started with a clear mission: make essential products easier to buy through transparent, easy-to-pay installment plans—especially for salaried customers who want quality goods without disrupting their monthly budget or taking unnecessary loans.</p>
<p>We continue to improve our packages, offers, and customer experience. Through our store network and user-friendly online shopping platform, we aim to set a high standard for electronics and home appliances retail. Reliability and trust remain at the heart of the Rafi &amp; Sons name.</p>

<h3>Our Vision</h3>
<p>With a vision to make Rafi &amp; Sons a leading store for installment shopping, we rely on modern methods, dedicated teams, and genuine products. We partner with trusted national and international brands and keep expanding our catalogue so customers can choose confidently.</p>
<p>Our priority is excellent customer service and high-quality products at competitive prices. We work to remove confusion around online and installment shopping so you can buy from the comfort of your home without worrying about authenticity or unfair pricing.</p>

<h3>Our Mission</h3>
<p>Our mission is to be a preferred shopping destination where customers can buy electronic appliances and consumer goods with official warranties and clear installment options. From day one, we follow a simple rule: put the customer’s interest first. That principle helps us earn trust and deliver consistent service.</p>
<p>We aim to bring practical, customer-friendly shopping standards to our market, extend our product lines and brand partnerships, and keep improving so you can find a wide range of electronics and household goods under one roof.</p>
<p>Today’s world is more digital than ever. Keeping that in mind, Rafi &amp; Sons continues to strengthen its online experience so customers can browse products, select a plan, and submit booking requests with ease.</p>
<p>Rafi &amp; Sons also focuses on easy installment plans. Whether you need a mobile phone, refrigerator, or another home appliance, you can explore affordable monthly options. Our goal is to help customers buy what they need without unnecessary financial pressure.</p>

<h3>Brands Available at Rafi &amp; Sons</h3>
<p>Why choose Rafi &amp; Sons instead of buying from one brand only? We bring multiple categories and brands together in one place, with clear pricing and installment options, so you can compare and decide what fits your needs and budget.</p>
<p>We take delivery and handling seriously. From warehouse to doorstep, our team works to ensure products reach you carefully and on time, subject to our delivery policy and verification process.</p>

<h3>Sale Events and Contests</h3>
<p>From time to time, Rafi &amp; Sons may introduce promotions, discounts, free delivery offers, and other campaigns to make shopping more rewarding. Event terms are announced on our website and official channels and may change without prior notice.</p>

<h3>Customer Support</h3>
<p>Nothing is more important to us than customer satisfaction. Our support team helps with product questions, installment bookings, and service queries. Please use the contact details published on our website for the fastest response.</p>
<p><em>Note: Support response times may vary depending on query volume. We appreciate your patience.</em></p>
HTML;
    }

    private function returnPolicy(): string
    {
        return <<<'HTML'
<h3>Return &amp; Refund Policy</h3>
<ul>
<li>RETURN is possible if the customer received the wrong item(s) from our end.</li>
<li>RETURN is accepted only when an item is unused, unscratched, and in the original packaging or the same condition as received at delivery.</li>
<li>Items will not be returned if they are found damaged, scratched, or used.</li>
<li>RETURN of wrong item(s) will be entertained only when the original invoice/receipt is presented.</li>
<li>Item(s) must be checked at the time of receiving an order; otherwise, return claims may not be entertained.</li>
<li>Item(s) such as AC, UPS, burner, range hood, and cooking range that cannot be fully checked on the spot must be inspected and any complaint registered within 5 working days of delivery.</li>
<li>The order will not be replaced/returned after the date and stamp have been placed on the warranty card.</li>
<li>Please check the order while receiving. After that, complaints may not be acceptable except as stated in this policy.</li>
<li>Product images on our website are for illustrative purposes only and may differ slightly from the actual product due to lighting, screen settings, or manufacturing updates.</li>
<li>Cubic feet (cft) measurements for refrigerators are approximate and provided for customer understanding; actual capacity may vary slightly.</li>
<li>No refund will be processed without special approval from higher authorities. Terms &amp; conditions apply.</li>
<li>Where approved, refunds are typically processed within <strong>7 to 10 working days</strong> in cases such as:
<ol>
<li>The product price was applied incorrectly by mistake.</li>
<li>The product price changed but had not yet been updated on the website.</li>
</ol>
</li>
</ul>
<p>We strive to keep pricing and product information accurate. In rare cases human error may occur. We apologize for any inconvenience and appreciate your understanding.</p>
HTML;
    }

    private function deliveryPolicy(): string
    {
        return <<<'HTML'
<h3>Floor Delivery</h3>
<ul>
<li>Heavy items such as ACs, refrigerators, and deep freezers may be delivered up to a limited floor level depending on location and access.</li>
<li>If the access path is spacious, this service may be provided at no extra cost where applicable.</li>
<li>For smaller or congested spaces, additional charges may apply based on the situation at the time of delivery.</li>
</ul>

<h3>Local Delivery</h3>
<ul>
<li>Product inspection may be available upon delivery, but change of mind during the delivery attempt is not acceptable.</li>
<li>Inspection is limited to checking for damage, missing components, or verifying the correct item.</li>
<li>Our delivery staff may check electronic appliances on delivery to help satisfy the customer, but due to time constraints they will not operate the product extensively.</li>
<li>Please ensure you are satisfied and have checked your product properly at the time of delivery.</li>
</ul>

<h3>Payment Options at Delivery</h3>
<ul>
<li>Cash on Delivery (COD) may be available subject to order type, location, and verification.</li>
<li>Advance payment may be required for certain locations, higher-value COD orders, or out-of-area deliveries.</li>
<li>Delivery is typically attempted after confirmation via phone call within a few working days, subject to stock and verification.</li>
<li>Delivery fees are charged based on location and order value. Exact charges are confirmed before dispatch.</li>
</ul>

<h3>Out of City</h3>
<ul>
<li>Advance payment may be required before dispatching.</li>
<li>Incomplete addresses may require advance payment.</li>
<li>Delivery charges apply separately based on city/zone.</li>
<li>For higher-value orders, full advance payment may be required.</li>
<li>Delivery timelines after payment confirmation are typically several working days and will be communicated by our team.</li>
</ul>

<h3>Note</h3>
<ul>
<li>For lower-value orders, delivery charges may apply nationwide.</li>
<li>For furniture-specific rules, please see our Furniture Policy page.</li>
</ul>
HTML;
    }

    private function terms(): string
    {
        return <<<'HTML'
<h3>Terms &amp; Conditions</h3>
<p>At Rafi &amp; Sons we work to provide quality products and services. To keep that experience smooth and trustworthy, please read these key terms and conditions.</p>

<h4>Availability</h4>
<p>Some products, deals, and discounts—especially during sale seasons—may be available only online and in limited quantity.</p>

<h4>Cancellation</h4>
<p>You may request cancellation of an order for a valid reason before it is dispatched. Once dispatched, cancellation is subject to our return and delivery policies.</p>

<h4>Change in Prices</h4>
<p>Please stay connected through our website and official channels for the most accurate prices and discounts. Prices, deals, and packages are subject to change without prior notice.</p>

<h4>Customer Engagement</h4>
<p>We engage with customers through electronic, non-electronic, and social platforms. Information shared there may be updated or changed at any time without prior notice.</p>

<h4>Delivery</h4>
<p>We try to deliver as soon as possible, but unexpected factors such as weather, route blockage, or strikes may cause delay. In such cases we are not responsible for delay or failure beyond our control.</p>

<h4>Incorrect Information</h4>
<p>Please double-check your details before submitting an order. We are not responsible for incorrect information provided by the customer.</p>

<h4>Modification</h4>
<p>We reserve the right to modify products, services, offers, discounts, and deals at any time to better serve customers.</p>

<h4>Right to Refuse</h4>
<p>We reserve the right to refuse service in situations such as product unavailability, payment delay, incomplete verification, or other operational reasons.</p>

<h4>Unauthorized Use</h4>
<p>By using our services you agree not to reuse or redistribute our content or services for commercial or unauthorized purposes without prior permission.</p>

<h4>Warranty</h4>
<p>Where applicable, products are sold with the original brand warranty. Warranty claims should be directed to the manufacturer’s / brand’s warranty or customer service centers.</p>

<h4>Promotional Activities</h4>
<p>We are not responsible for misinterpretation of promotional posts and banners. Any promotional voucher is generally valid for one use only unless stated otherwise.</p>

<h4>Trademark</h4>
<p>Trademarks, logos, and graphics on our site may not be used for services not provided by Rafi &amp; Sons.</p>

<h4>Typographical Errors</h4>
<p>Despite our best efforts, typographical errors regarding pricing or descriptions may occur. We reserve the right to correct errors at any time without prior notice.</p>

<h4>Customer Feedback</h4>
<p>We reserve the right to use or publish customer recommendations, comments, suggestions, and reviews submitted to us through various platforms.</p>
<p>We may update these terms at any time. It is your responsibility to review the latest version on our website.</p>
<p><strong>By using the Rafi &amp; Sons website you agree to the terms and conditions mentioned above.</strong></p>
HTML;
    }

    private function privacy(): string
    {
        return <<<'HTML'
<h3>Privacy Statement</h3>
<p>This website is owned and operated by Rafi &amp; Sons (referred to as “Rafi &amp; Sons”, “we”, “us”, or “our”). We are committed to protecting the privacy of visitors while they interact with our website.</p>
<p>We may collect tracking information when you visit or use our website. We may also receive and record information on our server logs from your browser, including IP address, cookie information, and the page requested.</p>

<h4>Section 1 – What Do We Do With Your Information?</h4>
<p>When you place an order or booking request, we collect personal information you provide such as name, address, phone number, and email address.</p>
<p>When you browse our store, we may receive your computer’s IP address to help us understand browser and operating system usage.</p>
<p>Email marketing (if applicable): with your permission, we may send emails about our store, products, and updates.</p>

<h4>Section 2 – Consent</h4>
<p><strong>How do you get my consent?</strong><br>
When you provide personal information to complete a transaction, place an order, arrange delivery, or return a purchase, we imply that you consent to collecting and using it for that specific reason.</p>
<p>If we ask for personal information for a secondary reason such as marketing, we will ask for consent or provide an option to decline.</p>
<p><strong>How do I withdraw my consent?</strong><br>
You may withdraw consent for continued collection, use, or disclosure of your information by contacting us through the details published on our Contact page.</p>

<h4>Section 3 – Disclosure</h4>
<p>We may disclose personal information if required by law or if you violate our Terms of Service.</p>

<h4>Section 4 – Payment</h4>
<p>If you pay by card or through a payment gateway, card data is handled by the payment provider over secure channels. We do not store full card details on our servers.</p>

<h4>Section 5 – Links</h4>
<p>Links on our store may direct you to other sites. We are not responsible for the privacy practices of those sites and encourage you to read their privacy statements.</p>

<h4>Section 6 – Security</h4>
<p>We take reasonable precautions and follow industry best practices to protect personal information from loss, misuse, unauthorized access, disclosure, alteration, or destruction.</p>

<h4>Section 7 – Cookies</h4>
<p>A cookie is a small text file saved to your device and retrieved on later visits. We may use cookies to enhance and simplify your visit. We do not use cookies to disclose personal information to third parties for unrelated purposes.</p>
<p>Permanent cookies may be stored on your device for a limited period. Session cookies disappear when you close your browser. You can erase cookies using your browser settings.</p>
<p>We may use third-party analytics cookies in aggregate form to understand site usage.</p>

<h4>Section 8 – Age of Consent</h4>
<p>By using this site, you represent that you are of the age of majority in your place of residence, or that you have consent for any minor dependents to use this site.</p>

<h4>Section 9 – Changes to This Privacy Policy</h4>
<p>We reserve the right to modify this privacy policy at any time. Changes take effect when posted on the website. If our business is acquired or merged, your information may be transferred so we can continue serving you.</p>

<h4>Questions</h4>
<p>To access, correct, amend, or delete personal information we hold about you, or to ask questions about this policy, please contact us using the details on our Contact page.</p>
HTML;
    }

    private function paymentPolicy(): string
    {
        return <<<'HTML'
<h3>Cash on Delivery (COD)</h3>
<ul>
<li>COD may be available for eligible orders within Pakistan, subject to verification and location.</li>
<li>When your order arrives, pay the specified amount in cash to the courier or delivery staff as indicated on the delivery slip.</li>
<li>Please verify your order number against your confirmation details.</li>
<li>Additional COD handling rules or advance requirements may apply for certain areas or order values.</li>
</ul>

<h3>Online / Card Payments</h3>
<p>Where enabled, Rafi &amp; Sons may offer secure online payment options through authenticated gateways. Bank or gateway charges (if any) may be borne by the customer as stated at checkout.</p>

<h3>Bank Transfer / Internet Banking</h3>
<ol>
<li>Log in to your internet banking app.</li>
<li>Add Rafi &amp; Sons as a beneficiary using the official account details shared by our team.</li>
<li>Transfer the required amount and save the payment screenshot.</li>
<li>Share the screenshot with our team through the official WhatsApp/email/contact channels published on our website.</li>
</ol>
<p><strong>Note:</strong> Official bank account details are provided by Rafi &amp; Sons support. Do not transfer money to unofficial accounts.</p>

<h3>Support Notes</h3>
<ol>
<li><strong>Exchanges or returns:</strong> Our Return &amp; Refund Policy applies.</li>
<li><strong>Chargebacks:</strong> Contact your bank for assistance with chargeback processes.</li>
<li><strong>Refunds:</strong> Approved refunds may take several working days to complete.</li>
<li>Card or gateway verification may take up to a few working days before dispatch.</li>
<li>For bank transfers and gateway payments, share proof of payment so we can verify and process your order.</li>
<li>Ensure your account is enabled for e-commerce/online payments via your bank if required.</li>
<li>If an order paid by card is cancelled due to change of mind, bank charges already incurred may not be refundable.</li>
</ol>
<p>For help, please use the contact details on our website.</p>
HTML;
    }

    private function installmentPolicy(): string
    {
        return <<<'HTML'
<h3>Installment Policy</h3>
<ol>
<li><strong>Advance payment:</strong> Customers may be required to pay an advance portion of the total order amount as confirmed for the selected plan.</li>
<li><strong>Bank account:</strong> Having a bank account is preferable but may not be mandatory, subject to verification requirements.</li>
<li><strong>Charges / markup:</strong> Plan pricing, monthly installment, and any processing charges are shown on the product/plan details before you submit a booking.</li>
<li><strong>Product choice:</strong> Customers may choose eligible products available on installment plans from our catalogue.</li>
<li><strong>Guarantors / documents:</strong> Guarantors and documentation may be required as part of verification. Exact requirements are confirmed by our team.</li>
<li><strong>Documentation charges:</strong> Documentation or processing fees may apply and will be communicated before confirmation.</li>
<li><strong>Delivery service:</strong> Doorstep documentation and delivery may be offered subject to location and product type.</li>
<li><strong>Delivery charges:</strong> Delivery terms follow our Delivery Policy; free delivery may apply only where explicitly offered.</li>
</ol>
<p>For installment assistance on mobiles, bikes, appliances, and other eligible products, contact us through the numbers and channels published on our website.</p>
<p><strong>NOTE:</strong> We do not offer installments on property/real estate, cars, gold, or jewelry unless explicitly stated otherwise.</p>
<p>Rafi &amp; Sons reserves the right to approve or decline installment bookings based on verification. Before product handover, the customer may be required to sign documents confirming plan details and terms. Customers must read and understand these terms before purchasing on installments.</p>
<p><em>Submitting a booking on our website is a request only and is not automatic financing approval.</em></p>
HTML;
    }

    private function furniturePolicy(): string
    {
        return <<<'HTML'
<h3>Furniture Policy</h3>

<p><strong>Fabric warranty:</strong><br>
We do not provide a warranty for fabric-related issues unless explicitly stated for a specific product.</p>

<p><strong>Damage warranty:</strong><br>
Damage claims caused by the customer are handled as repair only where possible. Replacement is not guaranteed.</p>

<p><strong>Mirror warranty:</strong><br>
There is generally no warranty for mirrors unless stated otherwise.</p>

<p><strong>Repair warranty:</strong><br>
Repair and maintenance coverage may be offered for a limited period as confirmed at purchase (commonly up to 1 year where applicable).</p>

<p><strong>Delivery and fitting:</strong><br>
Delivery and fitting charges may apply depending on location and product. Online offers for free delivery/fitting apply only when clearly stated on the product or campaign.</p>

<p><strong>Refund and replacement process:</strong><br>
Refunds and replacements are processed after inspection by our team. If a product is non-repairable, replacement requires approval from management.</p>

<p><strong>Change of mind:</strong><br>
We do not offer refunds, returns, or exchanges for change of mind or personal preference changes, except where required by our Return Policy for wrong/defective delivery cases.</p>

<p><strong>Refunds in rare scenarios:</strong><br>
Refunds are considered only in rare situations and require approval from higher management.</p>

<p>To register a complaint, please use the helpline, WhatsApp, or email published on our Contact page during business hours.</p>
HTML;
    }
}
