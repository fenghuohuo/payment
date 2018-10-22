<?php

namespace Fenghuohuo\Payment\PaymentAdapter;

interface AdapterInterface
{
    /**
     * @return mixed
     */
    public function setConfigure();

    /**
     * @param \Stdclass $payInfo
     * @return mixed
     */
    public function getCredential(\Stdclass $payInfo);

    /**
     * @param $notifyData
     * @return mixed
     */
    public function checkNotify($notifyData);
}