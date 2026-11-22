<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    //
    protected $fillable = ['client_id','title','description','category','budget','deadline','status'];
    protected $casts = ['deadline' => 'datetime'];
    public function client() { return $this->belongsTo(User::class, 'client_id'); }
    public function bids()   { return $this->hasMany(Bid::class); }
    
}
