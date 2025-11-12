<?php

namespace App\Services;

use Weble\ZohoClient\OAuthClient;
use Weble\ZohoClient\Enums\Region;
use Webleit\ZohoBooksApi\ZohoBooks;
use Webleit\ZohoBooksApi\Client as ZohoBooksClient;
use Illuminate\Support\Facades\Log;
use Exception;

/**
 * Zoho Books Service
 *
 * Service for integrating with Zoho Books API using webleit/zohobooksapi
 *
 * @package App\Services
 */
class ZohoBooksService
{
    /**
     * Zoho Books Client instance
     */
    private ?ZohoBooks $client = null;

    /**
     * Organization ID
     */
    private string $organizationId;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->organizationId = config('services.zoho.organization_id', '');
        $this->initializeClient();
    }

    /**
     * Initialize Zoho Books Client
     *
     * @throws Exception
     */
    private function initializeClient(): void
    {
        $clientId = config('services.zoho.client_id');
        $clientSecret = config('services.zoho.client_secret');
        $refreshToken = config('services.zoho.refresh_token');
        $region = config('services.zoho.region', 'us');

        if (empty($clientId) || empty($clientSecret) || empty($refreshToken)) {
            throw new Exception('Zoho configuration is incomplete. Please check ZOHO_CLIENT_ID, ZOHO_CLIENT_SECRET, and ZOHO_REFRESH_TOKEN in .env file.');
        }

        if (empty($this->organizationId)) {
            throw new Exception('ZOHO_ORGANIZATION_ID is required in .env file.');
        }

        try {
            // Initialize OAuth Client
            $oAuthClient = new OAuthClient($clientId, $clientSecret);
            $oAuthClient->setRefreshToken($refreshToken);

            // Enable offline mode for automatic token refresh
            $oAuthClient->offlineMode();

            // Set region (Region is an abstract class with constants)
            $regionValue = match (strtolower($region)) {
                'eu' => Region::EU,
                'in' => Region::IN,
                'au' => Region::AU,
                'jp' => Region::JP,
                'cn' => Region::CN,
                'sa' => Region::SA,
                default => Region::US,
            };
            $oAuthClient->setRegion($regionValue);

            // Initialize Zoho Books Client
            $zohoBooksClient = new ZohoBooksClient($oAuthClient);
            $zohoBooksClient->setOrganizationId($this->organizationId);

            // Initialize Zoho Books
            $this->client = new ZohoBooks($zohoBooksClient);
        } catch (Exception $e) {
            Log::error('Failed to initialize Zoho Books client', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            throw new Exception('Failed to initialize Zoho Books client: ' . $e->getMessage());
        }
    }
    /**
     * Get Zoho Books Client instance
     *
     * @return ZohoBooks
     * @throws Exception
     */
    private function getClient(): ZohoBooks
    {
        if (!$this->client) {
            $this->initializeClient();
        }

        return $this->client;
    }

    /**
     * Create or get customer in Zoho Books
     *
     * @param array $customerData Customer data
     * @return array Customer data from Zoho
     * @throws Exception
     */
    public function createOrGetCustomer(array $customerData): array
    {
        try {
            $client = $this->getClient();

            // Try to find existing customer by email
            $email = $customerData['email'] ?? null;

            if ($email) {
                try {
                    $contacts = $client->contacts->getList([
                        'email' => $email,
                    ]);

                    if ($contacts->count() > 0) {
                        $customer = $contacts->first();
                        Log::info('Zoho customer found by email', [
                            'customer_id' => $customer->getId(),
                        ]);
                        // Convert Model to array
                        $customerData = $customer->toArray();
                        return [
                            'contact_id' => $customer->getId(),
                            'contact_name' => $customerData['contact_name'] ?? '',
                            'email' => $customerData['email'] ?? '',
                            'phone' => $customerData['phone'] ?? '',
                        ];
                    }
                } catch (Exception $e) {
                    Log::warning('Failed to search for existing Zoho customer', [
                        'email' => $email,
                        'error' => $e->getMessage(),
                    ]);
                }
            }

            // Create new customer (All in English)
            $contactData = [
                'contact_name' => $customerData['name'] ?? 'Customer',
                'email' => $email,
                'phone' => $customerData['phone'] ?? '',
                'billing_address' => [
                    'address' => $customerData['address'] ?? '',
                    'city' => $customerData['city'] ?? '',
                    'state' => $customerData['state'] ?? '',
                    'zip' => $customerData['postal_code'] ?? '',
                    'country' => $customerData['country'] ?? '',
                ],
            ];

            $customer = $client->contacts->create($contactData);

            Log::info('Zoho customer created successfully', [
                'customer_id' => $customer->getId(),
            ]);

            // Convert Model to array
            $customerData = $customer->toArray();
            return [
                'contact_id' => $customer->getId(),
                'contact_name' => $customerData['contact_name'] ?? '',
                'email' => $customerData['email'] ?? '',
                'phone' => $customerData['phone'] ?? '',
            ];
        } catch (Exception $e) {
            Log::error('Failed to create or get Zoho customer', [
                'error' => $e->getMessage(),
                'customer_data' => $customerData,
            ]);
            throw new Exception('Failed to create or get Zoho customer: ' . $e->getMessage());
        }
    }

    /**
     * Create invoice in Zoho Books
     *
     * @param array $invoiceData Invoice data
     * @return array Invoice data from Zoho
     * @throws Exception
     */
    public function createInvoice(array $invoiceData): array
    {
        try {
            $client = $this->getClient();

            // Prepare line items (All in English)
            $lineItems = [];
            foreach (($invoiceData['line_items'] ?? []) as $item) {
                $lineItems[] = [
                    'name' => $item['name'] ?? '',
                    'description' => $item['description'] ?? '',
                    'rate' => (float) ($item['rate'] ?? 0),
                    'quantity' => (int) ($item['quantity'] ?? 1),
                ];
            }

            $invoicePayload = [
                'customer_id' => $invoiceData['customer_id'],
                'date' => $invoiceData['date'] ?? date('Y-m-d'),
                'due_date' => $invoiceData['due_date'] ?? date('Y-m-d', strtotime('+30 days')),
                'currency_code' => $invoiceData['currency_code'] ?? 'USD',
                'line_items' => $lineItems,
                'notes' => $invoiceData['notes'] ?? '',
                'terms' => $invoiceData['terms'] ?? '',
                // Set template if provided
                'template_id' => $invoiceData['template_id'] ?? null,
            ];

            // Use reference_number instead of invoice_number to avoid conflicts
            // Zoho will auto-generate invoice_number
            if (!empty($invoiceData['invoice_number'])) {
                $invoicePayload['reference_number'] = $invoiceData['invoice_number'];
            }

            // Remove null and empty values
            $invoicePayload = array_filter($invoicePayload, fn($value) => $value !== null && $value !== '');

            $invoice = $client->invoices->create($invoicePayload);

            // Convert Model to array
            $invoiceData = $invoice->toArray();

            Log::info('Zoho invoice created successfully', [
                'invoice_id' => $invoice->getId(),
                'invoice_number' => $invoiceData['invoice_number'] ?? null,
            ]);

            return [
                'invoice_id' => $invoice->getId(),
                'invoice_number' => $invoiceData['invoice_number'] ?? null,
            ];
        } catch (Exception $e) {
            Log::error('Failed to create Zoho invoice', [
                'error' => $e->getMessage(),
                'invoice_data' => $invoiceData,
            ]);
            throw new Exception('Failed to create Zoho invoice: ' . $e->getMessage());
        }
    }

    /**
     * Download invoice PDF
     *
     * @param string $invoiceId Zoho invoice ID
     * @return string PDF content
     * @throws Exception
     */
    public function downloadInvoicePdf(string $invoiceId): string
    {
        try {
            $client = $this->getClient();

            // Get invoice first
            $invoice = $client->invoices->get($invoiceId);

            // Mark invoice as sent to ensure PDF is complete with all headers and data
            // This ensures the invoice is in a finalized state before PDF generation
            try {
                $invoice->markAsSent();
                Log::info('Invoice marked as sent before PDF download', ['invoice_id' => $invoiceId]);
            } catch (Exception $e) {
                // If already sent, this will fail but that's okay
                Log::debug('Invoice may already be sent', ['invoice_id' => $invoiceId, 'error' => $e->getMessage()]);
            }

            // Wait a moment for Zoho to process
            sleep(1);

            // Get fresh invoice data after marking as sent
            $invoice = $client->invoices->get($invoiceId);

            // Use getPdf() method from Document model
            // This adds 'accept' => 'pdf' query parameter
            $pdf = $invoice->getPdf();

            return (string) $pdf;
        } catch (Exception $e) {
            Log::error('Failed to download Zoho invoice PDF', [
                'invoice_id' => $invoiceId,
                'error' => $e->getMessage(),
            ]);
            throw new Exception('Failed to download invoice PDF: ' . $e->getMessage());
        }
    }
}
