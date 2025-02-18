<?php defined('BASEPATH') OR exit('No direct script access allowed');

use Kreait\Firebase\JWT\CustomTokenGenerator;

Class Test extends CI_Controller
{
  public function index()
  {
    $clientEmail = 'firebase-adminsdk-wq6c2@boxity-app.iam.gserviceaccount.com';
    $privateKey = '-----BEGIN PRIVATE KEY-----\nMIIEvAIBADANBgkqhkiG9w0BAQEFAASCBKYwggSiAgEAAoIBAQDlEYOlOZ4Xuedg\nUk8T//P0lriL9W/PEo2JbDrJBMTN2nlX8SkXvuz9HJkbhIaDm+tdEhqpkCZ5EAD8\nSYWk2QBDa/bbgiII5WgyllH4ShNHQokzLFOHnuKbkk42Vl4EaBCrYZCN0QhRVlZh\n/fBPY6TQvhD+/WVOGH4DEU2nccK1IdeTeidtyXBV9gAKF86JZ5nf5gQXToUGaL5n\nx5ZMMev6klr88KF268fZT2miiSXG667CRofGBmOlFELe5TR0I/KCY/2aOnOKrXOo\nhwOn9aMcyNjaxwZXWi8zPa17x/um4GRwLxbu36ja1yHZYk1xr2Qlfx5Mh4bflxu0\nh6K/RkQJAgMBAAECggEAFlewO7YkO6O0E/LLSEPbj2SBEROqKWwhloESNceri3mP\nAtsgmzKR6nk23NZ/CUi0uwH4TEglXIMJWZIxQRwyWKEn9Y6ak7wOfvqm+Zz+BuIQ\nQEfHSO/gIfTyAH5Jksv0rkaooxB7q3VMnSe34AJz0bFsNnbQQf8FEXcHWDjyGRkJ\n71JpADGXNlnpabIvbfb7WSoRxk41EnRuwEmmG9XeotWn+WghhkGcSQBWg8VhRZ5n\nOFocnSvZfD0Rkx+j15xgC+ujNHJX1G4fNqr88q4GU7JLrmIUx1EPDSqv7qPy08IJ\naEjnk40O9QNCo8hDsCvmi7FSSI5tuBhVagCojwrPQQKBgQD15wbyb5mv64DcQbnR\nPlV679kRAEoEWOz4LkMY4p06Ky8Mgrb5v0cKle36Tl7+iXkpupaIt6Wdnt6qYV5K\nerNe+7QLUbSLmomp9rqXy0/DkRRFRPG5HvWIR1MSXtw86ABk007QI9Cwt/aJ/BWQ\nY7N/B1OZIP8N4a9Q9seL8PtAIQKBgQDueYZJhgwh2y3aVuVUCtOXtUXPlPiIdeqA\nCnbtd//JD2I7n0F/VT0sF8r7Z26k/gsmorEhCTaWEAoCprVST4kKI09BGvna+K6c\nN8XPBng82ZCT6yVh1E74l2eQMldyYodTEiz3B0QX4b/wBP3dOeTCHyqhTItqSHav\nQ7WsjJQm6QKBgGVtXZgyIBpH8WboVVTReUC0Hby9ecpBQ706l8Jz9pY/qbBnWkG5\ne/wJy5crLFOhMLDdnanW1iElnoJ4lwPxiHrtJ15j3SyYGaBZfK2P1t67wLixr2LE\nOlJz2PgC2KTmrQLpheCkZTf/KVnQ+LQN68PBqeHHkmyPYljq8XPvejMBAoGAZG4p\nb61UTq3PkbQmwE4O7kUZMWK7iDyglWvSyg9PWY1xAUsAem+bY4ZhpZ7ZqgKTD6JU\nlmUa5/e5P4SVuCRmwi48ol6J+v3gC0oxtA41dOrrSGAvThPrbiXVJ5UL3tA/zWxK\nI36b3rLj8mEnyJMpE9esTGHAFPKPiSAq0pAhlIECgYAhfoEEoKUmiKfhLOpQ/h+e\nvi1zU6KD4TBIFYAi5jO48zairon+6Ofh62YtEVm7qW+TWJLpern0MH6im6WmbhSE\n2E34ZItMt9d3ciWuZxtrKE0iwv1h/IM5fQmu0vmXN+5PA6cLwjBmthehnVTeMUh8\n0+2w6rldcOpWFM7T0zHiIQ==\n-----END PRIVATE KEY-----\n';

    $generator = CustomTokenGenerator::withClientEmailAndPrivateKey($clientEmail, $privateKey);
    $token = $generator->createCustomToken('uid', ['first_claim' => 'first_value' /* ... */]);

    echo $token;
      
  }
  
}