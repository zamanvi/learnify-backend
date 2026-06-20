@if ($message = Session::get('success'))
    <div class="row" id="successMessage">
        <div class="col-sm-12 col-lg-6"></div>
        <div class="col-sm-12 col-lg-6 d-flex justify-content-between alert text-white bg-primary" role="alert">
            <div class="iq-alert-icon">
                <i class="lar la-check-circle"></i>
            </div>
            <div class="iq-alert-text text text-white"><strong>{{ $message }}</strong></div>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <i class="ri-close-line"></i>
            </button>
        </div>
    </div>
@endif
@if ($message = Session::get('error'))
    <div class="row" id="successMessage">
        <div class="col-sm-12 col-lg-6"></div>
        <div class="col-sm-12 col-lg-6 d-flex justify-content-between alert text-white bg-secondary" role="alert">
            <div class="iq-alert-icon">
                <i class="ri-information-line"></i>
            </div>
            <div class="iq-alert-text text text-primary"><strong>{{ $message }}</strong></div>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <i class="ri-close-line"></i>
            </button>
        </div>
    </div>
@endif
@if ($message = Session::get('warning'))
    <div class="row">
        <div class="col-sm-12 col-lg-6"></div>
        <div class="col-sm-12 col-lg-6 d-flex justify-content-between alert text-white bg-warning" role="alert">
            <div class="iq-alert-icon">
                <i class="ri-alert-line"></i>
            </div>
            <div class="iq-alert-text text text-primary"><strong>{{ $message }}</strong></div>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <i class="ri-close-line"></i>
            </button>
        </div>
    </div>
@endif
@if ($message = Session::get('info'))
    <div class="row">
        <div class="col-sm-12 col-lg-6"></div>
        <div class="col-sm-12 col-lg-6 d-flex justify-content-between alert text-white bg-info" role="alert">
            <div class="iq-alert-icon">
                <i class="ri-information-line"></i>
            </div>
            <div class="iq-alert-text text text-primary"><strong>{{ $message }}</strong></div>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <i class="ri-close-line"></i>
            </button>
        </div>
    </div>
@endif
{{-- @if ($errors->any())
    <div class="col-sm-12 d-flex justify-content-between alert text-white bg-info"
        role="alert">
        <div class="iq-alert-icon">
            <i class="ri-information-line"></i>
        </div>
        <div class="iq-alert-text text text-primary">
            @foreach ($errors->all() as $error)
                <strong>
                    {{ $error }}
                </strong>
            @endforeach
        </div>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <i class="ri-close-line"></i>
        </button>
    </div>
@endif --}}
