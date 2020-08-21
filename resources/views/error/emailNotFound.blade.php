@extends('layouts.client')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="error-wrapper">
                    <div class="error-image">
                        <i class="icon-cup"></i>
                    </div>
                    <div class="error-email">
                        <h2>Oops!</h2>
                        <p>We're sorry, we are temporarily unable to provide service
                            using this venue, please email us at <a href="mailto:support@chat.com" target="_blank">support@netcov.com</a> or call <a href="tel:1234567">(978) 739-8060</a> for
                            immediate assistance. We hope to hear from you soon!</p>
                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection