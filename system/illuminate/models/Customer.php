<?php namespace App\Eloquent;

class Customer extends EncapsulatedEloquentBase
{
	protected $table = 'customer';
	protected $primaryKey = 'customer_id';
	const CREATED_AT = 'date_added';
	const UPDATED_AT = 'date_modified';

	protected $fillable = array('firstname','lastname','email','telephone','fax','wishlist','newsletter');

	public function addresses()
	{
		return $this->hasMany('App\Eloquent\Address');
	}
	public function address()
	{
		return $this->belongsTo('App\Eloquent\Address');
	}

	public static function register($params)
	{
		$customer = Customer::create($params);
		$salt = substr(md5(uniqid(rand(), true)), 0, 9);
		$customer->status = 1;
		$customer->salt = $salt;
		$customer->password = sha1($salt.sha1($salt.sha1($params['password'])));
		$customer->save();

		$customer->addAddress($params, true);

		return $customer;
	}

	public function addAddress($params, $default = false)
	{
		$address = Address::create($params);
		$this->addresses()->save($address);
		if ($default) {
			$this->address()->associate($address);
			$this->save();
		}
	}
	public function getAddresses()
	{
		$addresses_data = array();
		foreach ($this->addresses as $address) {
			$addresses_data[$address->address_id] = $address->getData();
		}
		return $addresses_data;
	}
}
