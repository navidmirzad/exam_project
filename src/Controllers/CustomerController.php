<?php


namespace src\Controllers;
use src\Models\Customer;
require_once 'src/Models/Customer.php';

class CustomerController {

    private Customer $customerModel;

    public function __construct() {
        $this->customerModel = new Customer();
    }

    public function getAllCustomers() {
        $customers = $this->customerModel->getAll();
        http_response_code(200);
        echo json_encode($customers);
        return;
    }

}