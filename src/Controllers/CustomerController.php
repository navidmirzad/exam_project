<?php

namespace src\Controllers;
use src\Models\Customer;
require_once 'src/Models/Customer.php';

class CustomerController {

    private Customer $customer;

    public function __construct() {
        $this->customer = new Customer();
    }

    public function getAll() {

        try {
            $customers = $this->customer->getAll();
            http_response_code(200);
            echo json_encode($customers);
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
    }

}