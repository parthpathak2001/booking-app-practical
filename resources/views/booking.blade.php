@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Booking Form</div>
                <div class="card-body">
                    
                    @if (session('success'))
                        <div class="alert alert-success" role="alert">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="alert alert-danger" role="alert">
                            {{ session('error') }}
                        </div>
                    @endif
                    
                    <form method="POST" action="{{ route('book.store') }}">
                        @csrf
                        <div class="mb-3">
                            <label for="customer_name" class="form-label">Customer Name</label>
                            <input type="text" class="form-control" id="customer_name" name="customer_name" value="{{old('customer_name')}}" required>
                        </div>

                        <div class="mb-3">
                            <label for="customer_email" class="form-label">Customer Email</label>
                            <input type="email" class="form-control" id="customer_email" name="customer_email" value="{{old('customer_email')}}" required>
                        </div>

                        <div class="mb-3">
                            <label for="booking_date" class="form-label">Booking Date</label>
                            <input type="date" class="form-control" id="booking_date" name="booking_date" value="{{old('booking_date')}}" required>
                        </div>

                        <div class="mb-3">
                            <label for="booking_type" class="form-label">Booking Type</label>
                            <select class="form-select" id="booking_type" name="booking_type" required>
                                <option value="full_day" {{ old('booking_type') == 'full_day' ? 'selected' : '' }}>Full Day</option>
                                <option value="half_day" {{ old('booking_type') == 'half_day' ? 'selected' : '' }}>Half Day</option>
                                <option value="custom" {{ old('booking_type') == 'custom' ? 'selected' : '' }}>Custom</option>
                            </select>
                        </div>

                        <div class="mb-3" id="booking_slot_section" style="{{ old('booking_type') == 'half_day' ? 'display: block;' : 'display: none;' }}">
                            <label for="booking_slot" class="form-label">Booking Slot</label>
                            <select class="form-select" id="booking_slot" name="booking_slot">
                                <option value="first_half" {{ old('booking_slot') == 'first_half' ? 'selected' : '' }}>First Half</option>
                                <option value="second_half" {{ old('booking_slot') == 'second_half' ? 'selected' : '' }}>Second Half</option>
                            </select>
                        </div>

                        <div class="row" id="booking_time_section" style="{{ old('booking_type') == 'custom' ? 'display: flex;' : 'display: none;' }}">
                            <div class="col-md-6">
                                <label for="booking_from_time" class="form-label">From Time</label>
                                <input type="time" class="form-control" id="booking_from_time" name="booking_from_time" value="{{old('booking_from_time')}}">
                            </div>
                            <div class="col-md-6">
                                <label for="booking_to_time" class="form-label">To Time</label>
                                <input type="time" class="form-control" id="booking_to_time" name="booking_to_time" value="{{old('booking_to_time')}}">
                            </div>
                        </div>

                        <div class="mt-3">
                            <button type="submit" class="btn btn-primary">Submit Booking</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.getElementById('booking_type').addEventListener('change', function() {
        let slotSection = document.getElementById('booking_slot_section');
        let timeSection = document.getElementById('booking_time_section');

        if (this.value === 'half_day') {
            slotSection.style.display = 'block';
            timeSection.style.display = 'none';
        } else if (this.value === 'custom') {
            slotSection.style.display = 'none';
            timeSection.style.display = 'flex';
        } else {
            slotSection.style.display = 'none';
            timeSection.style.display = 'none';
        }
    });
</script>

@endsection
