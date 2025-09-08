<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lead extends Model {
    use HasFactory;

    protected $fillable = [
        'name', 'contact', 'stage', 'value', 'status'
    ];

    public function client()
{
    return $this->hasOne(Client::class, 'email', 'contact'); // if `contact` holds the email
}

public function shouldBecomeClient()
{
    return $this->stage === 'Closed' && $this->status === 'won';
}



};