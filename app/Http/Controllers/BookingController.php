<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Booking;

class BookingController extends Controller
{
    public function create()
    {
        return view('booking');
    }

    public function store(Request $request)
    {
        $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_email' => 'required|email',
            'booking_date' => 'required|date',
            'booking_type' => 'required',
            'booking_slot' => 'required_if:booking_type,half_day',
            'booking_from_time' => 'required_if:booking_type,custom',
            'booking_to_time' => 'required_if:booking_type,custom',
        ]);

        $bookingDate = $request->booking_date;
        $bookingType = $request->booking_type;
        $bookingSlot = $request->booking_slot;
        $fromTime = $request->booking_from_time;
        $toTime = $request->booking_to_time;


        // Check for duplicate bookings
        $existingBooking = Booking::where('booking_date', $bookingDate)
            ->where(function ($query) use ($bookingType, $bookingSlot, $fromTime, $toTime) {
                if ($bookingType === 'full_day') {
                    // Full day booking blocks everything
                    $query->where('booking_type', 'full_day')
                        ->orWhere('booking_type', 'half_day')
                        ->orWhere('booking_type', 'custom');
                } elseif ($bookingType === 'half_day') {
                    // Half day - First Half (12 AM - 12 PM)
                    if ($bookingSlot === 'first_half') {
                        $query->where('booking_type', 'full_day')
                            ->orWhere(function ($q) {
                                $q->where('booking_type', 'half_day')->where('booking_slot', 'first_half');
                            })
                            ->orWhere(function ($q) {
                                $q->where('booking_type', 'custom')
                                    ->whereBetween('booking_from_time', ['00:00:00', '12:00:00']);
                            });
                    }
                    // Half day - Second Half (12 PM - 12 AM)
                    elseif ($bookingSlot === 'second_half') {
                        $query->where('booking_type', 'full_day')
                            ->orWhere(function ($q) {
                                $q->where('booking_type', 'half_day')->where('booking_slot', 'second_half');
                            })
                            ->orWhere(function ($q) {
                                $q->where('booking_type', 'custom')
                                    ->whereBetween('booking_from_time', ['12:00:01', '23:59:59']);
                            });
                    }
                } elseif ($bookingType === 'custom') {
                    // Custom booking prevents full-day and overlapping bookings
                    $query->where('booking_type', 'full_day')
                        ->orWhere(function ($q) use ($fromTime) {
                            if ($fromTime >= '00:00:00' && $fromTime < '12:00:00') {
                                $q->where('booking_type', 'half_day')->where('booking_slot', 'first_half');
                            } elseif ($fromTime >= '12:00:01' && $fromTime <= '23:59:59') {
                                $q->where('booking_type', 'half_day')->where('booking_slot', 'second_half');
                            }
                        })
                        ->orWhere(function ($q) use ($fromTime, $toTime) {
                            $q->where('booking_type', 'custom')
                                ->where(function ($innerQ) use ($fromTime, $toTime) {
                                    $innerQ->whereBetween('booking_from_time', [$fromTime, $toTime])
                                        ->orWhereBetween('booking_to_time', [$fromTime, $toTime]);
                                });
                        });
                }
            })
            ->exists();

        if ($existingBooking) {
            return back()->with('error', 'Booking slot is already taken.')->withInput();
        }

        // Save Booking
        Booking::create([
            'customer_name' => $request->customer_name,
            'customer_email' => $request->customer_email,
            'booking_date' => $request->booking_date,
            'booking_type' => $request->booking_type,
            'booking_slot' => $request->booking_type === 'half_day' ? $request->booking_slot : null,
            'booking_from_time' => $request->booking_type === 'custom' ? $request->booking_from_time : null,
            'booking_to_time' => $request->booking_type === 'custom' ? $request->booking_to_time : null,
        ]);
    
        return redirect()->route('book.create')->with('success', 'Booking successful!');
    }
}
