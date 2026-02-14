# Hosted-Checkout
MHosted Checkout General Information You Need To Know:

The Hosted Checkout integration method is ideal when you want a solution that is both secure and fast to deploy. In this integration, you create a checkout session where the Mastercard Gateway hosts a payment page UI that gathers payment information from the customer, so you need not handle sensitive data at all.

The main purpose of using the Hosted Checkout payment method:

Integration process is simple and quick.
You do not need to handle or store any payment details, which may lower PCI compliance costs.
The main integration steps consist of the following steps:

Establish a checkout session

Request a checkout session using the INITIATE CHECKOUT operation.

Implement the Hosted Payment Page
Show the payer either an Embedded Page or a Payment Page and start the payment process. Optionally, include callbacks for handling events that occur during the payment interaction, such as the payer cancelling the payment, the session timing out, or redirecting the payer to another website to finish payment.

Interpret the response
Receive the results of the payment from the gateway and update your system with the payment details. Return the payer to your web site and display the payment receipt to them.
