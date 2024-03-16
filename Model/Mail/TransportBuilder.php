<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Theiconnz\Wholesale\Model\Mail;
class TransportBuilder extends \Magento\Framework\Mail\Template\TransportBuilder
{
    public function addAttachment($pdfString, $pdfname="signup.pdf")
    {
        $this->message->createAttachment(
            $pdfString,
    'application/pdf',
            \Zend_Mime::DISPOSITION_ATTACHMENT,
            \Zend_Mime::ENCODING_BASE64,
            'attatched.pdf'
        );
        return $this;
    }
}
