# Hosted-Checkout
# Mastercard MPGS PHP cURL Demo (Hosted Checkout)

This project demonstrates a simple integration with the **Mastercard Payment Gateway Services (MPGS)** sandbox using PHP and cURL.

It shows how to:

1. Create a Hosted Checkout session
2. Display the embedded payment page
3. Receive the gateway redirect
4. Retrieve and parse order results

---

## Files

### 1) curl_Demo.php

Creates a checkout session and displays the embedded payment form.

- Calls MPGS API: `/session`
- Generates a random Order ID
- Loads Mastercard `checkout.min.js`
- Shows the payment page

### 2) curl_Receipt.php

Handles the gateway redirect after payment.

- Receives `OrderId` from URL
- Calls MPGS API: `/order/{OrderId}`
- Parses transaction results
- Displays authentication and payment response

---

## Requirements

- PHP 7.4+ (PHP 8+ recommended)
- cURL enabled in PHP
- HTTPS-enabled web server
- MPGS Sandbox credentials

---

## Setup

### Step 1 — Upload Files

Place both files in your web server directory:
/public_html/
├── curl_Demo.php
└── curl_Receipt.php


---

### Step 2 — Configure curl_Demo.php

Edit the configuration section:

```php
$merchant      = "Company Name";     // Display name
$currency      = "USD";              // Must match your MPGS account
$merchant_id   = "YOUR_MERCHANT_ID";
$api_password  = "YOUR_API_PASSWORD";
$returnUrl     = "https://www.yourwebsite.com/curl_Receipt.php";

**Important — Return URL**

**Must be a public HTTPS URL**
 - Must point to curl_Receipt.php
 - Do NOT add query parameters
 - Gateway will append OrderId automatically

Example redirect after payment and received parameters:
https://www.yourwebsite.com/curl_Receipt.php?OrderId=TEST-20&resultIndicator=bf9307f336cd4d63&sessionVersion=bf477f0609&checkoutVersion=1.0.0

**HOW IT WORKS (FLOW)**

**1. Open Demo Page**
Navigate to:
https://www.yourwebsite.com/curl_Demo.php


**2. Session Creation**
The script performs the following actions:
- Sends an INITIATE_CHECKOUT request to MPGS
- Receives a session ID from the gateway
- Loads the embedded Mastercard checkout page


**3. Customer Payment**
The customer enters card details on the secure Mastercard-hosted payment page.


**4. Gateway Redirect**
After payment processing, MPGS redirects the browser to:
curl_Receipt.php?OrderId=TEST-XX


**5. Order Retrieval**
curl_Receipt.php then:
- Reads the OrderId from the URL
- Requests order details from MPGS
- Parses the transaction[] array
- Displays authentication and payment results



**OUTPUT EXAMPLES**

Authentication Result:
Cardholder Authentication = Approved

Payment Result:
Transaction Type: PAYMENT
Acquirer Code: 00
Acquirer Message: Approved



**SANDBOX NOTES**

- Uses the MPGS test gateway (sandbox)
- No real money is charged
- Official Mastercard test cards can be used



**TROUBLESHOOTING**

No redirect or blank page:
- Ensure $returnUrl is correct and publicly accessible
- Verify HTTPS is enabled


cURL Error:
Check that:
- PHP cURL extension is installed and enabled
- Firewall allows outbound HTTPS traffic (port 443)


HTTP Error from Gateway:
Verify:
- Merchant ID and API password are correct
- Sandbox endpoint URL is correct
- The Order ID exists



**PRODUCTION USE**

Before going live:
- Switch to the production gateway URL
- Use production credentials
- Implement proper validation and logging
- Never expose API credentials in public repositories

