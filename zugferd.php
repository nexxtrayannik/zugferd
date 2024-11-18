<?php

namespace ZUGFeRD;

class ZUGFeRDGenerator {

    private $data = [];

    public function addField($fieldName, $value) {
        $this->data[$fieldName] = $value;
    }

    public function createXML() {
        $xml = new \SimpleXMLElement('<rsm:CrossIndustryInvoice xmlns:rsm="urn:ferd:CrossIndustryDocument:invoice:1p0" xmlns:ram="urn:un:unece:uncefact:data:standard:ReusableAggregateBusinessInformationEntity:12" xmlns:udt="urn:un:unece:uncefact:data:standard:UnqualifiedDataType:15"/>');

        // ExchangedDocumentContext
        $context = $xml->addChild('rsm:ExchangedDocumentContext');
        $contextParam = $context->addChild('ram:GuidelineSpecifiedDocumentContextParameter');
        $contextParam->addChild('ram:ID', 'urn:ferd:CrossIndustryDocument:invoice:1p0:comfort');

        // ExchangedDocument
        $document = $xml->addChild('rsm:ExchangedDocument');
        $document->addChild('ram:ID', $this->data['fldInvoiceNo']);
        $document->addChild('ram:TypeCode', '380');
        $issueDateTime = $document->addChild('ram:IssueDateTime');
        $issueDateTime->addChild('udt:DateTimeString', $this->data['fldTimeStamp'])->addAttribute('format', '102');

        // SupplyChainTradeTransaction
        $transaction = $xml->addChild('rsm:SupplyChainTradeTransaction');

        // ApplicableHeaderTradeAgreement
        $agreement = $transaction->addChild('ram:ApplicableHeaderTradeAgreement');
        $seller = $agreement->addChild('ram:SellerTradeParty');
        $seller->addChild('ram:Name', $this->data['fldFirmenname']);
        $sellerAddress = $seller->addChild('ram:PostalTradeAddress');
        $sellerAddress->addChild('ram:LineOne', $this->data['fldStrasse']);
        $sellerAddress->addChild('ram:PostcodeCode', $this->data['fldPLZ']);
        $sellerAddress->addChild('ram:CityName', $this->data['Ort']);
        $sellerAddress->addChild('ram:CountryID', $this->data['Land']);
        $sellerTax = $seller->addChild('ram:SpecifiedTaxRegistration');
        $sellerTax->addChild('ram:ID', $this->data['fldVATID'])->addAttribute('schemeID', 'VA');

        $buyer = $agreement->addChild('ram:BuyerTradeParty');
        $buyer->addChild('ram:Name', $this->data['AddressEmpfaenger']['name']);
        $buyerAddress = $buyer->addChild('ram:PostalTradeAddress');
        $buyerAddress->addChild('ram:LineOne', $this->data['AddressEmpfaenger']['street']);
        $buyerAddress->addChild('ram:PostcodeCode', $this->data['AddressEmpfaenger']['postcode']);
        $buyerAddress->addChild('ram:CityName', $this->data['AddressEmpfaenger']['city']);
        $buyerAddress->addChild('ram:CountryID', $this->data['AddressEmpfaenger']['country']);

        // ApplicableHeaderTradeDelivery
        $delivery = $transaction->addChild('ram:ApplicableHeaderTradeDelivery');
        $deliveryEvent = $delivery->addChild('ram:ActualDeliverySupplyChainEvent');
        $deliveryEvent->addChild('ram:OccurrenceDateTime')->addChild('udt:DateTimeString', $this->data['fldTimeStamp'])->addAttribute('format', '102');

        // ApplicableHeaderTradeSettlement
        $settlement = $transaction->addChild('ram:ApplicableHeaderTradeSettlement');
        $settlement->addChild('ram:PaymentReference', $this->data['fldNummer']);
        $settlement->addChild('ram:InvoiceCurrencyCode', $this->data['Waehrung']);
        $paymentMeans = $settlement->addChild('ram:SpecifiedTradeSettlementPaymentMeans');
        $paymentMeans->addChild('ram:TypeCode', '30');
        $financialAccount = $paymentMeans->addChild('ram:PayeePartyCreditorFinancialAccount');
        $financialAccount->addChild('ram:IBANID', $this->data['fldIBAN']);
        $financialInstitution = $paymentMeans->addChild('ram:PayeeSpecifiedCreditorFinancialInstitution');
        $financialInstitution->addChild('ram:BICID', 'BIC12345678'); // Beispiel-BIC

        $tax = $settlement->addChild('ram:ApplicableTradeTax');
        $tax->addChild('ram:TypeCode', 'VAT');
        $tax->addChild('ram:CategoryCode', $this->data['invoiceTax']['categoryCode']);
        $tax->addChild('ram:RateApplicablePercent', $this->data['invoiceTax']['rate']);

        $monetarySummation = $settlement->addChild('ram:SpecifiedTradeSettlementMonetarySummation');
        $monetarySummation->addChild('ram:LineTotalAmount', $this->data['TaxBasisTotalAmount']);
        $monetarySummation->addChild('ram:ChargeTotalAmount', '0.00');
        $monetarySummation->addChild('ram:AllowanceTotalAmount', '0.00');
        $monetarySummation->addChild('ram:TaxBasisTotalAmount', $this->data['TaxBasisTotalAmount']);
        $monetarySummation->addChild('ram:TaxTotalAmount', $this->data['InvVATValue']);
        $monetarySummation->addChild('ram:GrandTotalAmount', $this->data['BruttoSum']);
        $monetarySummation->addChild('ram:DuePayableAmount', $this->data['BruttoSum']);

        // IncludedSupplyChainTradeLineItem
        foreach ($this->data['items'] as $index => $itemData) {
            $lineItem = $transaction->addChild('ram:IncludedSupplyChainTradeLineItem');
            $lineDocument = $lineItem->addChild('ram:AssociatedDocumentLineDocument');
            $lineDocument->addChild('ram:LineID', $index + 1);

            $tradeProduct = $lineItem->addChild('ram:SpecifiedTradeProduct');
            $tradeProduct->addChild('ram:Name', $itemData['Leistung']);

            $tradeAgreement = $lineItem->addChild('ram:SpecifiedLineTradeAgreement');
            $grossPrice = $tradeAgreement->addChild('ram:GrossPriceProductTradePrice');
            $grossPrice->addChild('ram:ChargeAmount', $itemData['Betrag']);
            $netPrice = $tradeAgreement->addChild('ram:NetPriceProductTradePrice');
            $netPrice->addChild('ram:ChargeAmount', $itemData['Betrag']);

            $tradeDelivery = $lineItem->addChild('ram:SpecifiedLineTradeDelivery');
            $tradeDelivery->addChild('ram:BilledQuantity', $itemData['BasisQuantity'])->addAttribute('unitCode', 'C62');

            $tradeSettlement = $lineItem->addChild('ram:SpecifiedLineTradeSettlement');
            $lineTax = $tradeSettlement->addChild('ram:ApplicableTradeTax');
            $lineTax->addChild('ram:TypeCode', 'VAT');
            $lineTax->addChild('ram:CategoryCode', $this->data['invoiceTax']['categoryCode']);
            $lineTax->addChild('ram:RateApplicablePercent', $this->data['invoiceTax']['rate']);

            $lineMonetarySummation = $tradeSettlement->addChild('ram:SpecifiedTradeSettlementLineMonetarySummation');
            $lineMonetarySummation->addChild('ram:LineTotalAmount', $itemData['Betrag']);
        }

        return $xml->asXML();
    }

    public function saveXML($xml, $filename) {
        file_put_contents($filename, $xml);
    }
}
