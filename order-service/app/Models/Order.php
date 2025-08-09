<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Support\Carbon as LaravelCarbon;

/**
 * Order Model
 * 
 * @property int $id
 * @property string $order_number
 * @property int $lapangan_id
 * @property int|null $jadwal_lapangan_id
 * @property string $customer_name
 * @property string $customer_email
 * @property string $customer_phone
 * @property string $tanggal_booking
 * @property string $jam_mulai
 * @property string $jam_selesai
 * @property float $total_harga
 * @property string $status
 * @property string $payment_status
 * @property string|null $notes
 * @property array|null $lapangan_info
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * 
 * @property-read string $jam_mulai_formatted
 * @property-read string $jam_selesai_formatted
 * @property-read \Carbon\Carbon $jam_mulai_carbon
 * @property-read \Carbon\Carbon $jam_selesai_carbon
 * 
 * @method static \Illuminate\Database\Eloquent\Builder|Order byCustomer(string $email)
 * @method static \Illuminate\Database\Eloquent\Builder|Order byStatus(string $status)
 * @method static \Illuminate\Database\Eloquent\Builder|Order byTanggalBooking(string $tanggal)
 * @method static \Illuminate\Database\Eloquent\Builder|Order newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Order newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Order query()
 */
class Order extends Model
{
    protected $fillable = [
        'order_number',
        'lapangan_id',
        'customer_name',
        'customer_email',
        'customer_phone',
        'phone', // untuk kompatibilitas dengan tabel yang ada
        'booking_date', // menggunakan field database yang sebenarnya
        'start_time',   // menggunakan field database yang sebenarnya  
        'end_time',     // menggunakan field database yang sebenarnya
        'tanggal_booking', // tetap ada untuk kompatibilitas
        'jam_mulai',    // tetap ada untuk kompatibilitas
        'jam_selesai',  // tetap ada untuk kompatibilitas
        'total_harga',
        'total_price',  // untuk kompatibilitas dengan tabel yang ada
        'status',
        'payment_status',
        'notes',
        'lapangan_info'
    ];

    protected $casts = [
        'tanggal_booking' => 'date',
        'jam_mulai' => 'string',
        'jam_selesai' => 'string',
        'total_harga' => 'decimal:2',
        'lapangan_info' => 'array'
    ];

    // Boot method untuk generate order number otomatis
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($order) {
            if (empty($order->order_number)) {
                $order->order_number = 'ORD-' . date('Ymd') . '-' . strtoupper(Str::random(6));
            }
        });
    }

    // Scope untuk filter berdasarkan customer email
    public function scopeByCustomer($query, $email)
    {
        return $query->where('customer_email', $email);
    }

    // Accessor untuk format jam yang konsisten
    public function getJamMulaiFormattedAttribute()
    {
        if (empty($this->jam_mulai)) {
            return '';
        }
        
        // Jika sudah dalam format HH:MM
        if (preg_match('/^\d{2}:\d{2}$/', $this->jam_mulai)) {
            return $this->jam_mulai;
        }
        
        // Jika dalam format datetime atau timestamp
        try {
            return date('H:i', strtotime($this->jam_mulai));
        } catch (\Exception $e) {
            return $this->jam_mulai;
        }
    }

    public function getJamSelesaiFormattedAttribute()
    {
        if (empty($this->jam_selesai)) {
            return '';
        }
        
        // Jika sudah dalam format HH:MM
        if (preg_match('/^\d{2}:\d{2}$/', $this->jam_selesai)) {
            return $this->jam_selesai;
        }
        
        // Jika dalam format datetime atau timestamp
        try {
            return date('H:i', strtotime($this->jam_selesai));
        } catch (\Exception $e) {
            return $this->jam_selesai;
        }
    }

    // Scope untuk filter berdasarkan status
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    // Scope untuk filter berdasarkan tanggal booking
    public function scopeByTanggalBooking($query, $tanggal)
    {
        return $query->where('tanggal_booking', $tanggal);
    }

    // Method untuk mengupdate status order
    public function updateStatus($status)
    {
        $this->update(['status' => $status]);
    }

    // Method untuk mengupdate payment status
    public function updatePaymentStatus($paymentStatus)
    {
        $this->update(['payment_status' => $paymentStatus]);
    }

    // Method untuk mendapatkan total durasi booking dalam jam
    public function getDurationInHours()
    {
        try {
            /** @var \Carbon\Carbon $start */
            $start = \Carbon\Carbon::createFromFormat('H:i', $this->jam_mulai);
            /** @var \Carbon\Carbon $end */
            $end = \Carbon\Carbon::createFromFormat('H:i', $this->jam_selesai);
            return $end->diffInHours($start);
        } catch (\Exception $e) {
            return 0;
        }
    }

    // Accessor untuk mendapatkan jam mulai sebagai Carbon object
    public function getJamMulaiCarbonAttribute()
    {
        try {
            /** @var \Carbon\Carbon $carbon */
            $carbon = \Carbon\Carbon::createFromFormat('H:i', $this->jam_mulai);
            return $carbon;
        } catch (\Exception $e) {
            /** @var \Carbon\Carbon $now */
            $now = \Carbon\Carbon::now();
            return $now;
        }
    }

    // Accessor untuk mendapatkan jam selesai sebagai Carbon object
    public function getJamSelesaiCarbonAttribute()
    {
        try {
            /** @var \Carbon\Carbon $carbon */
            $carbon = \Carbon\Carbon::createFromFormat('H:i', $this->jam_selesai);
            return $carbon;
        } catch (\Exception $e) {
            /** @var \Carbon\Carbon $now */
            $now = \Carbon\Carbon::now();
            return $now;
        }
    }

    // Method untuk mendapatkan durasi dalam format yang mudah dibaca
    public function getDurationFormatted()
    {
        $duration = $this->getDurationInHours();
        return $duration . ' jam';
    }

    // Accessor untuk mapping field database ke field yang diharapkan
    public function getBookingDateAttribute($value)
    {
        return $this->tanggal_booking ?? $value;
    }

    public function setBookingDateAttribute($value)
    {
        $this->attributes['booking_date'] = $value;
        $this->attributes['tanggal_booking'] = $value;
    }

    public function getStartTimeAttribute($value)
    {
        return $this->jam_mulai ?? $value;
    }

    public function setStartTimeAttribute($value)
    {
        $this->attributes['start_time'] = $value;
        $this->attributes['jam_mulai'] = $value;
    }

    public function getEndTimeAttribute($value)
    {
        return $this->jam_selesai ?? $value;
    }

    public function setEndTimeAttribute($value)
    {
        $this->attributes['end_time'] = $value;
        $this->attributes['jam_selesai'] = $value;
    }

    public function getTotalPriceAttribute($value)
    {
        return $this->total_harga ?? $value;
    }

    public function setTotalPriceAttribute($value)
    {
        $this->attributes['total_price'] = $value;
        $this->attributes['total_harga'] = $value;
    }
}
