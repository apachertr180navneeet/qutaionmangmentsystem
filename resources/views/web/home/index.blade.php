@extends('web.layouts.app')
@section('content')
<!-- Ambient Glow Effects -->
<div class="bg-glow"></div>
<div class="bg-glow bg-glow-right"></div>
<div class="bg-glow bg-glow-left"></div>

<section class="min-vh-100 flex-center position-relative">
    <div class="container pt-100 pb-100">
        <div class="row justify-content-center align-items-center">
            <div class="col-lg-8 col-md-10 text-center">
                
                <div class="glass-panel text-center">
                    <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill px-3 py-2 mb-4 fw-medium" style="border: 1px solid rgba(99, 102, 241, 0.2);">
                        ✨ The Next Generation Platform
                    </span>
                    
                    <h1 class="display-4 fw-bolder mb-4">
                        Elevate Your Workflow with <br> 
                        <span class="text-gradient">Premium Management</span>
                    </h1>
                    
                    <p class="mb-5 mx-auto" style="max-width: 600px;">
                        Experience the ultimate quotation management system. Streamlined, beautiful, and built for modern teams that demand excellence.
                    </p>
                    
                    <div class="d-flex justify-content-center gap-3">
                        <a href="#" class="btn btn-premium px-5 py-3">Get Started</a>
                        <a href="#" class="btn btn-outline-light px-5 py-3 rounded-pill" style="border-color: rgba(255,255,255,0.2);">Learn More</a>
                    </div>
                </div>

            </div>
        </div>
    </div>
</section>
@endsection
@section('script')
<script>
    // Fade out preloader smoothly
    window.addEventListener('load', function() {
        setTimeout(function() {
            var preloader = document.querySelector('.preloader');
            if(preloader) {
                preloader.style.opacity = '0';
                setTimeout(function() {
                    preloader.style.display = 'none';
                }, 500);
            }
        }, 300);
    });
</script>
@endsection