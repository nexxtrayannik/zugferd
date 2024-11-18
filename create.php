<?php
require 'queue.php';

$queue = new InvoiceQueue();

$invoiceData1 = [
    'fldFirmenname' => 'Beispiel GmbH',
    'fldStrasse' => 'BeispielStr. 4',
    'fldPLZ' => '80807',
    'Ort' => 'München',
    'Land' => 'DE',
    'AddressEmpfaenger' => [
        'name' => 'Empfänger GmbH',
        'street' => 'Innovation Straße 5',
        'postcode' => '10243',
        'city' => 'Berlin',
        'country' => 'DE'
    ],
    'fldDebitor' => '123456',
    'fldIBAN' => 'DE89370400440532013000',
    'fldKtoBez' => 'Musterkonto',
    'fldNummer' => 'RE13',
    'fldInvoiceNo' => '123456789',
    'fldTimeStamp' => '20231',
    'fldVATID' => 'DE578398439',
    'fldAbrStart' => '20240101',
    'fldAbrEnd' => '20241231',
    'items' => [
        [
            'Leistung' => 'dgdgdgs',
            'BasisQuantity' => 1,
            'Betrag' => 27.38
        ],
        [
            'Leistung' => 'gsfsfsfs',
            'BasisQuantity' => 1,
            'Betrag' => 20.32
        ]
    ],
    'invoiceTax' => [
        'categoryCode' => 'S',
        'rate' => 19.00
    ],
    'TaxBasisTotalAmount' => 47.70,
    'NettoSum' => 47.71,
    'InvVATValue' => 9.06,
    'BruttoSum' => 56.76,
    'Waehrung' => 'EUR'
];

$invoiceData2 = [
    'fldFirmenname' => 'Beispiel GmbH 2',
    'fldStrasse' => 'BeispielStr. 5',
    'fldPLZ' => '80808',
    'Ort' => 'München',
    'Land' => 'DE',
    'AddressEmpfaenger' => [
        'name' => 'Empfänger GmbH 2',
        'street' => 'Innovation Straße 6',
        'postcode' => '10244',
        'city' => 'Berlin',
        'country' => 'DE'
    ],
    'fldDebitor' => '654321',
    'fldIBAN' => 'DE89370400440532013001',
    'fldKtoBez' => 'Musterkonto 2',
    'fldNummer' => 'RE1338',
    'fldInvoiceNo' => '987654321',
    'fldTimeStamp' => '20241101',
    'fldVATID' => 'DE578398440',
    'fldAbrStart' => '20240201',
    'fldAbrEnd' => '20241231',
    'items' => [
        [
            'Leistung' => 'produkt1',
            'BasisQuantity' => 2,
            'Betrag' => 50.00
        ],
        [
            'Leistung' => 'produkt2',
            'BasisQuantity' => 3,
            'Betrag' => 75.00
        ]
    ],
    'invoiceTax' => [
        'categoryCode' => 'S',
        'rate' => 19.00
    ],
    'TaxBasisTotalAmount' => 125.00,
    'NettoSum' => 125.00,
    'InvVATValue' => 23.75,
    'BruttoSum' => 148.75,
    'Waehrung' => 'EUR'
];

$queue->addToQueue($invoiceData1);
$queue->addToQueue($invoiceData2);

// Starten Sie den Queue-Worker separat, um die Warteschlange zu verarbeiten
// php worker.php
