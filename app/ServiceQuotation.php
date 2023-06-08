<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceQuotation extends Model
{
    use HasFactory;

    protected $table = 'service_quotations';
    protected $fillable =[
        "quotation_id", "service_id", "qty" , "net_unit_price", "discount", "tax_rate", "tax", "total"
    ];
}
