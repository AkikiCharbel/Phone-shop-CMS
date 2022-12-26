<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
//use Spatie\Permission\Traits\HasPermissions;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use CrudTrait;
    use HasRoles;

//    use HasPermissions;
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'phone_number',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    protected $appends = [
        'amount_left',
    ];

    public function sellouts(): HasMany
    {
        return $this->hasMany(Sellout::class, 'customer_id');
    }

    public function amountLeft(): Attribute
    {
        return Attribute::make(
            get: function () {
                $selloutPayment = SelloutPayment::whereIn('sellout_id', $this->sellouts->pluck('id'))->sum('amount');
                $selloutAmount = $this->sellouts()->sum('amount');

                return $selloutAmount - $selloutPayment;
            }
        );
    }

    public function selloutsList(): Attribute
    {
        return Attribute::make(
            get: function () {
                $sellouts = [];
                foreach ($this->sellouts as $sellout) {
                    $sellouts[] = [
                        'amount' => $sellout->amount.' $',
                        'amount_left' => $sellout->amount - $sellout->selloutPayments()->sum('amount').' $',
                        'link' => '<a href="'.config('app.url').'/admin/sellout/'.$sellout->id.'/edit">Update Invoice</a>',
                    ];
                }

                return $sellouts;
            }
        );
    }
}
