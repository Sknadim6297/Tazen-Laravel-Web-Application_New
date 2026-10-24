<!-- /header -->
@extends('layouts.layout')
@section('styles')
   {{-- <link rel="stylesheet" href="{{ asset('admin/css/styles.css') }}" /> --}}
   <link rel="stylesheet" href="{{ asset('frontend/assets/css/newslidertwo.css') }}">
    <link rel="stylesheet" href="{{ asset('frontend/assets/css/newsliders.css') }}">
	<link rel="stylesheet" href="{{ asset('frontend/assets/css/responsive2.css') }}" media="screen and (max-width: 992px)">
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
	 <style>
        html, body {
            overflow-x: hidden;
            width: 100%;
        }

        main {
            overflow-x: hidden;
        }

		@media only screen and (min-width: 768px) and (max-width: 1024px) {

			.fun-facts-cards .container .card-two {
    			margin-top: 20px;
    
		}

		.fun-facts-cards .container .right .row .third-card .card-three{
			margin-top: 14px;
		}

		.fun-facts-cards .container .card {
			margin-right: 12px
		}

		}
		
    </style>
	<style>
    @media (max-width: 768px) {
        /* Popular Services Mobile */
        .popular-services-main-section {
            padding: 40px 0;
        }
        
        .popular-services-title {
            font-size: 1.8rem;
            margin-bottom: 30px;
        }
        
        .popular-service-card {
            width: 180px;
            height: 200px;
            min-width: 180px;
            min-height: 200px;
            max-width: 180px;
            max-height: 200px;
        }
        
        .service-graphic {
            height: 130px;
            padding: 12px;
        }
        
        .service-content {
            min-height: 70px;
            padding: 15px;
        }
        
        .service-title {
            font-size: 1rem;
        }
    }
    /* Popular Services Section Styles */
    .popular-services-main-section {
        background-color: white;
        padding: 60px 0;
    }

    .popular-services-title {
        font-size: 2.2rem;
        font-weight: 600;
        color: #333;
        margin-bottom: 40px;
        text-align: left;
    }

    .popular-services-scroll-container {
        position: relative;
        overflow: hidden;
    }

    .popular-services-wrapper {
        display: flex;
        gap: 20px;
        overflow-x: auto;
        scroll-behavior: smooth;
        padding-bottom: 10px;
        scrollbar-width: none;
        -ms-overflow-style: none;
    }

    .popular-services-wrapper::-webkit-scrollbar {
        display: none;
    }

    .popular-service-card {
        width: 200px;
        height: 275px;
        min-width: 200px;
        min-height: 275px;
        max-width: 200px;
        max-height: 275px;
        background: #0d1d53;
        border-radius: 15px;
        overflow: hidden;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        flex-shrink: 0;
        display: flex;
        flex-direction: column;
        text-decoration: none;
        color: inherit;
        cursor: pointer;
    }

    .service-graphic {
        height: 250px;
        overflow: hidden;
        display: flex;
        align-items: flex-start;
        justify-content: center;
        padding: 5px;
        flex-shrink: 0;
    }

    .service-mini-card {
        width: 100%;
        height: 100%;
        border-radius: 8px;
        position: relative;
        overflow: hidden;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .service-image {
        width: 200px !important;
        height: 150px !important;
 		/* margin-top: -10px; */
        object-fit: cover;
        opacity: 0.9;
        border-radius: 15px;
    }

    .service-content {
        padding: 20px;
        text-align: left;
        flex: 1;
        display: flex;
        align-items: center;
        justify-content: flex-start;
        min-height: 70px;
    }

    .service-title {
        font-size: 1.1rem;
        font-weight: 600;
        margin: 0;
        color: white;
    }

    /* Scroll Arrow */
    .scroll-arrow-right {
        position: absolute;
        right: 10px;
        top: 50%;
        transform: translateY(-50%);
        background: white;
        border: none;
        border-radius: 50%;
        width: 40px;
        height: 40px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        z-index: 10;
    }


    .scroll-arrow-right i {
        color: #2c5530;
        font-size: 1.2rem;
    }

    /* Mini Card Styles - White Background */
    .coding-card, .webdev-card, .video-card, .software-card, .seo-card, .architecture-card {
        background: #FFFFFF;
        border: 2px solid #000000;
    }

    /* New Card Content Styles */
    
    /* Common Elements */
    .dot {
        width: 6px;
        height: 6px;
        border-radius: 50%;
        background: rgba(44, 85, 48, 0.6);
        margin: 0 1px;
    }

    /* Coding Card */
    .code-screen {
        width: 100%;
        height: 100%;
        padding: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .terminal-window {
        width: 90%;
        height: 80%;
        background: #1a1a1a;
        border-radius: 4px;
        padding: 6px;
        color: #00ff00;
    }

    .terminal-header {
        display: flex;
        align-items: center;
        margin-bottom: 4px;
    }

    .terminal-dots {
        display: flex;
        gap: 3px;
    }

    .terminal-content {
        font-size: 0.6rem;
        font-family: monospace;
    }

    .code-line {
        color: #87CEEB;
        margin-bottom: 2px;
    }

    .cursor-blink {
        color: #00ff00;
        animation: blink 1s infinite;
    }

    @keyframes blink {
        0%, 50% { opacity: 1; }
        51%, 100% { opacity: 0; }
    }

    /* Website Dev Card */
    .website-preview {
        width: 100%;
        height: 100%;
        padding: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .browser-window {
        width: 90%;
        height: 80%;
        background: white;
        border-radius: 4px;
        overflow: hidden;
    }

    .browser-header {
        background: #f0f0f0;
        padding: 4px 6px;
        display: flex;
        align-items: center;
    }

    .browser-dots {
        display: flex;
        gap: 3px;
    }

    .website-content {
        padding: 8px;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 6px;
    }

    .web-logo {
        width: 16px;
        height: 16px;
        background: #2c5530;
        border-radius: 2px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 0.6rem;
        font-weight: bold;
    }

    .web-nav {
        display: flex;
        gap: 4px;
    }

    .nav-item {
        width: 12px;
        height: 2px;
        background: #ddd;
        border-radius: 1px;
    }

    /* Video Card */
    .video-editor {
        width: 100%;
        height: 100%;
        padding: 8px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 6px;
    }

    .video-player {
        width: 70%;
        height: 60%;
    }

    .video-screen {
        width: 100%;
        height: 100%;
        background: #333;
        border-radius: 4px;
        display: flex;
        align-items: center;
        justify-content: center;
        position: relative;
    }

    .video-content {
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .play-icon {
        color: white;
        font-size: 0.8rem;
    }

    .video-timeline {
        width: 80%;
    }

    .timeline-track {
        height: 4px;
        background: #ddd;
        border-radius: 2px;
        position: relative;
    }

    .timeline-progress {
        height: 100%;
        width: 60%;
        background: #2c5530;
        border-radius: 2px;
    }

    /* Software Card */
    .app-development {
        width: 100%;
        height: 100%;
        padding: 8px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 6px;
    }

    .app-window {
        width: 80%;
        height: 60%;
        background: white;
        border-radius: 4px;
        overflow: hidden;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }

    .app-header {
        background: #2c5530;
        padding: 4px 6px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .app-title {
        color: white;
        font-size: 0.6rem;
        font-weight: bold;
    }

    .app-content {
        padding: 6px;
    }

    .code-lines {
        display: flex;
        flex-direction: column;
        gap: 2px;
    }

    .code-line-small {
        height: 2px;
        background: #ddd;
        border-radius: 1px;
    }

    .code-line-small:nth-child(1) { width: 80%; }
    .code-line-small:nth-child(2) { width: 60%; }
    .code-line-small:nth-child(3) { width: 90%; }

    .app-icons {
        display: flex;
        gap: 4px;
    }

    .app-icon {
        width: 8px;
        height: 8px;
        background: #2c5530;
        border-radius: 2px;
    }

    /* SEO Card */
    .seo-dashboard {
        width: 100%;
        height: 100%;
        padding: 8px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 8px;
    }

    .search-engine {
        width: 90%;
        height: 40%;
    }

    .search-bar {
        width: 100%;
        height: 16px;
        background: white;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        border: 1px solid #ddd;
    }

    .search-icon {
        font-size: 0.6rem;
        color: #666;
    }

    .ranking-chart {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 4px;
    }

    .rank-bars {
        display: flex;
        align-items: end;
        gap: 2px;
        height: 20px;
    }

    .rank-bar {
        width: 4px;
        border-radius: 1px;
        background: #2c5530;
    }

    .rank-1 { height: 16px; }
    .rank-2 { height: 12px; }
    .rank-3 { height: 8px; }

    .ranking-text {
        font-size: 0.5rem;
        font-weight: bold;
        color: #2c5530;
    }

    /* Architecture Card */
    .design-studio {
        width: 100%;
        height: 100%;
        padding: 8px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 6px;
    }

    .blueprint {
        width: 90%;
        height: 70%;
        background: rgba(255,255,255,0.8);
        border-radius: 4px;
        position: relative;
        border: 1px solid #ddd;
    }

    .floor-plan {
        width: 100%;
        height: 100%;
        position: relative;
        padding: 6px;
    }

    .room-outline {
        width: 100%;
        height: 100%;
        border: 1px solid #4A90E2;
        border-radius: 2px;
        position: relative;
    }

    .room-furniture {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 4px;
    }

    .furniture-chair {
        width: 8px;
        height: 6px;
        background: #4A90E2;
        border-radius: 1px;
    }

    .furniture-table {
        width: 12px;
        height: 8px;
        background: #87CEEB;
        border-radius: 1px;
    }

    .design-tools {
        display: flex;
        gap: 4px;
        align-items: center;
    }

    .ruler {
        width: 12px;
        height: 2px;
        background: #4A90E2;
        border-radius: 1px;
    }

    .pencil {
        width: 8px;
        height: 2px;
        background: #87CEEB;
        border-radius: 1px;
    }
	.btn-custom{
		    background: linear-gradient(135deg, #152a70, #c51010, #f39c12);
			border: none;
			color: white;
	}
	.text{
		color: white;
	}
.login-popup-title {
        font-size: 1.5rem !important;
        color: white !important;
    }

    .login-popup-title .wave-icon {
        font-size: 1.8rem !important;
    }

    .login-popup-custom {
        background: linear-gradient(135deg, #152a70, #c51010, #f39c12) !important;
        color: white !important;
        border-radius: 15px !important;
        padding: 25px 20px !important;
    }

    .login-popup-btn {
        background-color: #1e0d60 !important;
        color: white !important;
        font-size: 1.2rem !important;
        font-weight: 600 !important;
        padding: 12px 30px !important;
        border-radius: 50px !important;
        border: none !important;
        box-shadow: 0 4px 15px rgba(0,0,0,0.2) !important;
    }

    .login-popup-btn:hover {
        transform: translateY(-3px) !important;
        box-shadow: 0 6px 20px rgba(0,0,0,0.3) !important;
    }
    
    .login-popup-close {
        color: white !important;
        opacity: 0.8 !important;
        font-size: 1.5rem !important;
    }
    
    .login-popup-close:hover {
        opacity: 1 !important;
    }

    /* Tazen Pro Section - Compact & Stylish */
    .tazen-pro-section-compact {
        position: relative;
        overflow: hidden;
    }

    .pro-compact-card {
        background: white;
        backdrop-filter: blur(10px);
        border-radius: 16px;
        padding: 30px;
        border: 1px solid #e0e0e0;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
    }

    .pro-branding {
        text-align: left;
    }

    .pro-logo-compact {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .brand-name-compact {
        font-size: 2.5rem;
        font-weight: 700;
        color: #1a1a2e;
        letter-spacing: -1px;
    }

    .pro-badge {
        background: linear-gradient(135deg, #f39c12, #c51010);
        color: white;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 700;
        letter-spacing: 1px;
    }

    .pro-subtitle {
        color: #555;
        font-size: 1rem;
        margin-bottom: 20px;
        line-height: 1.5;
    }

    .btn-pro-cta {
        background: linear-gradient(135deg, #152a70, #c51010, #f39c12);
        color: white;
        border: none;
        padding: 12px 28px;
        border-radius: 8px;
        font-weight: 600;
        font-size: 1rem;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        box-shadow: 0 4px 15px rgba(245, 156, 18, 0.3);
    }

    .btn-pro-cta:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(245, 156, 18, 0.4);
        color: white;
    }

    .pro-features-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 15px;
    }

    .pro-feature-card {
        background: #f8f9fa;
        border: 1px solid #e0e0e0;
        border-radius: 12px;
        padding: 20px;
        display: flex;
        align-items: flex-start;
        gap: 15px;
        transition: all 0.3s ease;
    }

    .pro-feature-card:hover {
        background: white;
        transform: translateY(-3px);
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
        border-color: #c51010;
    }

    .feature-icon-box {
        width: 45px;
        height: 45px;
        background: linear-gradient(135deg, #f39c12, #c51010);
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .feature-icon-box i {
        font-size: 1.2rem;
        color: white;
    }

    .feature-text h5 {
        color: #1a1a2e;
        font-size: 1rem;
        font-weight: 600;
        margin: 0 0 5px 0;
    }

    .feature-text p {
        color: #666;
        font-size: 0.85rem;
        margin: 0;
        line-height: 1.4;
    }

    /* Project Status Widget */
    .pro-visual {
        position: relative;
        height: 400px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .project-status-widget {
        position: absolute;
        top: 20px;
        right: 20px;
        z-index: 10;
    }

    .status-card {
        background: white;
        border-radius: 15px;
        padding: 25px;
        box-shadow: 0 8px 25px rgba(0,0,0,0.15);
        text-align: center;
        min-width: 250px;
    }

    .status-title {
        color: #2c5530;
        font-weight: 600;
        margin-bottom: 15px;
        font-size: 1.1rem;
    }

    .progress-info {
        margin-bottom: 20px;
    }

    .progress-percentage {
        font-size: 1.8rem;
        font-weight: 700;
        color: #4CAF50;
        margin-bottom: 5px;
    }

    .progress-steps {
        font-size: 0.9rem;
        color: #666;
    }

    .circular-progress {
        margin-bottom: 20px;
    }

    .progress-circle {
        position: relative;
        display: inline-block;
    }

    .progress-ring {
        transform: rotate(-90deg);
    }

    .progress-ring-circle {
        transition: stroke-dashoffset 0.5s ease-in-out;
    }

    .progress-text {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        font-size: 1.2rem;
        font-weight: 700;
        color: #4CAF50;
    }

    .profile-section {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 12px;
    }

    .profile-avatar img {
        width: 40px;
        height: 40px;
        object-fit: cover;
        border: 2px solid #4CAF50;
    }

    .profile-name {
        font-weight: 600;
        color: #2c5530;
        font-size: 0.9rem;
    }

    .profile-status {
        font-size: 0.8rem;
        color: #4CAF50;
        font-weight: 500;
    }

    /* Progress Chart */
    .progress-chart {
        position: absolute;
        bottom: 20px;
        left: 20px;
        background: white;
        border-radius: 15px;
        padding: 20px;
        box-shadow: 0 8px 25px rgba(0,0,0,0.15);
        min-width: 200px;
        z-index: 10;
    }

    .chart-container {
        text-align: center;
    }

    .chart-title {
        font-size: 1rem;
        font-weight: 600;
        color: #2c5530;
        margin-bottom: 15px;
    }

    .chart-bars {
        display: flex;
        align-items: end;
        justify-content: center;
        gap: 8px;
        height: 100px;
        margin-bottom: 10px;
    }

    .chart-bar {
        width: 20px;
        background: linear-gradient(to top, #4CAF50, #8BC34A);
        border-radius: 3px 3px 0 0;
        position: relative;
        transition: all 0.3s ease;
    }

    .chart-bar:hover {
        transform: scaleY(1.1);
    }

    .bar-label {
        position: absolute;
        bottom: -20px;
        left: 50%;
        transform: translateX(-50%);
        font-size: 0.7rem;
        color: #666;
        font-weight: 500;
    }

    .chart-value {
        font-size: 1.2rem;
        font-weight: 700;
        color: #4CAF50;
    }

    /* Pro Illustration */
    .pro-illustration {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        z-index: 1;
    }

    .people-illustration {
        position: relative;
        width: 100%;
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .person {
        position: absolute;
        z-index: 2;
    }

    .person-1 {
        left: 30%;
        top: 40%;
    }

    .person-2 {
        right: 30%;
        top: 35%;
    }

    .person-head {
        width: 40px;
        height: 40px;
        background: #ffdbac;
        border-radius: 50%;
        margin-bottom: 5px;
        position: relative;
    }

    .person-head::after {
        content: '';
        position: absolute;
        top: 10px;
        left: 8px;
        width: 8px;
        height: 8px;
        background: #333;
        border-radius: 50%;
    }

    .person-head::before {
        content: '';
        position: absolute;
        top: 10px;
        right: 8px;
        width: 8px;
        height: 8px;
        background: #333;
        border-radius: 50%;
    }

    .person-body {
        width: 30px;
        height: 50px;
        background: #4CAF50;
        border-radius: 15px 15px 5px 5px;
        margin: 0 auto;
    }

    .laptop {
        position: absolute;
        left: 50%;
        top: 60%;
        transform: translateX(-50%);
        width: 60px;
        height: 40px;
        background: #333;
        border-radius: 5px;
        z-index: 3;
    }

    .laptop::before {
        content: '';
        position: absolute;
        top: -5px;
        left: 5px;
        width: 50px;
        height: 8px;
        background: #666;
        border-radius: 3px;
    }

    /* Responsive Design for Compact Pro Section */
    @media (max-width: 992px) {
        .pro-features-grid {
            grid-template-columns: repeat(2, 1fr);
            gap: 12px;
        }
        
        .pro-feature-card {
            padding: 15px;
        }
        
        .feature-text h5 {
            font-size: 0.9rem;
        }
        
        .feature-text p {
            font-size: 0.8rem;
        }
    }

    @media (max-width: 768px) {
        .pro-compact-card {
            padding: 25px 20px;
        }
        
        .brand-name-compact {
            font-size: 2rem;
        }
        
        .pro-subtitle {
            font-size: 0.9rem;
        }
        
        .btn-pro-cta {
            padding: 10px 24px;
            font-size: 0.95rem;
        }
        
        .pro-features-grid {
            grid-template-columns: 1fr;
            gap: 10px;
            margin-top: 20px;
        }
        
        .feature-icon-box {
            width: 40px;
            height: 40px;
        }
        
        .feature-icon-box i {
            font-size: 1rem;
        }
        
        .pro-visual {
            height: 300px;
        }
        
        .project-status-widget {
            top: 10px;
            right: 10px;
        }
        
        .status-card {
            min-width: 200px;
            padding: 20px;
        }
        
        .progress-chart {
            bottom: 10px;
            left: 10px;
            min-width: 180px;
            padding: 15px;
        }
        
        .person-1 {
            left: 25%;
            top: 45%;
        }

        .person-2 {
            right: 25%;
            top: 40%;
        }
    }

    @media (max-width: 576px) {
        .pro-compact-card {
            padding: 20px 15px;
        }
        
        .brand-name-compact {
            font-size: 1.75rem;
        }
        
        .pro-badge {
            font-size: 0.65rem;
            padding: 3px 10px;
        }
        
        .btn-pro-cta {
            padding: 8px 20px;
            font-size: 0.9rem;
        }
        
        .pro-feature-card {
            padding: 12px;
        }
        
        .feature-text h5 {
            font-size: 0.85rem;
        }
        
        .feature-text p {
            font-size: 0.75rem;
        }
        
        .pro-visual {
            height: 250px;
        }
        
        .project-status-widget {
            top: 5px;
            right: 5px;
        }
        
        .status-card {
            min-width: 180px;
            padding: 15px;
        }
        
        .progress-chart {
            bottom: 5px;
            left: 5px;
            min-width: 160px;
            padding: 12px;
        }
        
        .person-head {
            width: 30px;
            height: 30px;
        }
        
        .person-body {
            width: 25px;
            height: 40px;
        }
        
        .laptop {
            width: 50px;
            height: 30px;
        }
    }

    /* Service Sections */
    .popular-services-section {
        margin-top: 20px;
    }

    .section-title {
        font-size: 2rem;
        font-weight: 700;
        color: #333;
        margin-bottom: 30px;
        text-align: left;
    }

    .popular-service-card {
        min-width: 200px;
        /* background: linear-gradient(135deg, #f39c12, #c51010, #152a70); */
        background-color: #152a70
        border-radius: 15px;
        overflow: hidden;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        transition: transform 0.3s ease;
        flex-shrink: 0;
    }


    .service-image {
        height: 120px;
        overflow: hidden;
    }

    .service-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .service-content {
        padding: 15px;
        color: white;
    }

    .service-name {
        font-size: 1rem;
        font-weight: 600;
        margin-bottom: 10px;
        color: white;
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        .section-title {
            font-size: 1.5rem;
        }
        
        .popular-service-card {
            min-width: 180px;
        }
        
        .service-image {
            height: 100px;
        }
        
        .service-content {
            padding: 12px;
        }
        
        .service-name {
            font-size: 0.9rem;
            margin-bottom: 8px;
        }
    }

    @media (max-width: 576px) {
        .popular-service-card {
            min-width: 160px;
        }
        
        .service-image {
            height: 90px;
        }
        
        .service-content {
            padding: 10px;
        }
        
        .service-name {
            font-size: 0.8rem;
            margin-bottom: 6px;
        }
    }

    /* Why Choose Us Section - New Design */
    .why-choose-section {
        background: linear-gradient(135deg, rgba(21, 42, 112, 0.03), rgba(243, 156, 18, 0.03)) !important;
        padding: 50px 0 !important;
    }

    .why-choose-container {
        display: flex;
        align-items: center;
        gap: 40px;
        background: linear-gradient(to right, rgba(21, 42, 112, 0.08) 0%, rgba(21, 42, 112, 0.08) 55%, white 55%, white 100%);
        border-radius: 16px;
        padding: 40px 30px;
        box-shadow: 0 8px 30px rgba(21, 42, 112, 0.12);
        border: 1px solid rgba(21, 42, 112, 0.1);
    }

    .why-choose-left {
        flex: 1;
        padding-right: 20px;
    }

    .why-choose-title {
        font-size: 2rem;
        font-weight: 700;
        background: linear-gradient(135deg, #152a70, #c51010);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        margin-bottom: 15px;
        text-align: left;
    }

    .why-choose-description {
        font-size: 1rem;
        color: #555;
        margin-bottom: 25px;
        line-height: 1.5;
        text-align: left;
    }

    .image-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 15px;
        max-width: 350px;
    }

    .image-item {
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 3px 12px rgba(21, 42, 112, 0.15);
        transition: all 0.3s ease;
        border: 2px solid transparent;
    }

    .image-item:hover {
        transform: translateY(-5px) scale(1.02);
        box-shadow: 0 5px 20px rgba(197, 16, 16, 0.2);
        border-color: rgba(243, 156, 18, 0.5);
    }

    .image-item img {
        width: 100%;
        height: 160px;
        object-fit: cover;
        display: block;
    }

    .why-choose-right {
        flex: 1;
        padding-left: 20px;
    }

    .reason-item {
        display: flex;
        align-items: flex-start;
        gap: 15px;
        margin-bottom: 20px;
        padding: 15px;
        background: white;
        border-radius: 10px;
        box-shadow: 0 2px 8px rgba(21, 42, 112, 0.08);
        transition: all 0.3s ease;
        border-left: 3px solid transparent;
    }

    .reason-item:hover {
        transform: translateX(8px);
        box-shadow: 0 4px 15px rgba(197, 16, 16, 0.15);
        border-left-color: #c51010;
    }

    .reason-number {
        background: linear-gradient(135deg, #152a70, #c51010, #f39c12);
        color: white;
        width: 45px;
        height: 45px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 1.1rem;
        flex-shrink: 0;
        box-shadow: 0 3px 12px rgba(197, 16, 16, 0.3);
    }

    .reason-content {
        flex: 1;
    }

    .reason-title {
        font-size: 1.15rem;
        font-weight: 600;
        color: #152a70;
        margin-bottom: 8px;
    }

    .reason-description {
        font-size: 0.95rem;
        color: #666;
        line-height: 1.5;
        margin: 0;
    }

    /* Responsive Design for Why Choose Us */
    @media (max-width: 992px) {
        .why-choose-container {
            flex-direction: column;
            gap: 30px;
            padding: 35px 25px;
            background: linear-gradient(to bottom, rgba(21, 42, 112, 0.08) 0%, rgba(21, 42, 112, 0.08) 50%, white 50%, white 100%);
        }

        .why-choose-left,
        .why-choose-right {
            padding: 0;
        }

        .why-choose-title {
            text-align: center;
            font-size: 1.8rem;
        }

        .why-choose-description {
            text-align: center;
        }

        .image-grid {
            max-width: 100%;
            margin: 0 auto;
        }
    }

    @media (max-width: 768px) {
        .why-choose-section {
            padding: 40px 0 !important;
        }
        
        .why-choose-container {
            padding: 25px 20px;
            background: linear-gradient(to bottom, rgba(21, 42, 112, 0.08) 0%, rgba(21, 42, 112, 0.08) 50%, white 50%, white 100%);
        }
        
        .why-choose-title {
            font-size: 1.6rem;
        }
        
        .image-grid {
            grid-template-columns: 1fr 1fr;
            gap: 12px;
        }
        
        .image-item img {
            height: 140px;
        }
        
        .reason-item {
            padding: 12px;
            margin-bottom: 15px;
        }
        
        .reason-number {
            width: 40px;
            height: 40px;
            font-size: 1rem;
        }
        
        .reason-title {
            font-size: 1.05rem;
        }
        
        .reason-description {
            font-size: 0.9rem;
        }
    }

    @media (max-width: 576px) {
        .why-choose-section {
            padding: 30px 0 !important;
        }
        
        .why-choose-container {
            padding: 20px 15px;
        }
        
        .why-choose-title {
            font-size: 1.4rem;
        }
        
        .why-choose-description {
            font-size: 0.95rem;
        }
        
        .image-grid {
            gap: 10px;
        }
        
        .image-item img {
            height: 120px;
        }
        
        .reason-item {
            flex-direction: column;
            text-align: center;
            gap: 12px;
            padding: 15px;
        }
        
        .reason-number {
            align-self: center;
        }
    }

    /* New Launches Section Styling */
    .new-launches-section {
        background-color: #f8f9fa !important;
        padding: 80px 0 !important;
    }

    .launch-card {
        background: white;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 6px 20px rgba(0,0,0,0.08);
        transition: all 0.3s ease;
        height: 100%;
        cursor: pointer;
        position: relative;
    }

    .launch-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 12px 30px rgba(0,0,0,0.12);
    }

    .launch-image {
        height: 180px;
        overflow: hidden;
        position: relative;
    }

    .launch-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.3s ease;
    }

    .launch-card:hover .launch-image img {
        transform: scale(1.08);
    }

    .launch-content {
        padding: 20px 18px;
        text-align: center;
    }

    .launch-title {
        font-size: 1.2rem;
        font-weight: 600;
        color: #333;
        margin-bottom: 8px;
    }

    .launch-description {
        font-size: 0.9rem;
        color: #666;
        margin: 0;
        line-height: 1.4;
    }

    /* Interactive hover effect for launch cards */
    .launch-card::after {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: linear-gradient(135deg, rgba(255,152,0,0.1), rgba(255,87,34,0.1));
        opacity: 0;
        transition: opacity 0.3s ease;
        pointer-events: none;
    }

    .launch-card:hover::after {
        opacity: 1;
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        .new-launches-section {
            padding: 60px 0 !important;
        }
        
        .launch-card {
            margin-bottom: 20px;
        }
        .launch-image {
            height: 150px;
        }
        
        .launch-content {
            padding: 18px 15px;
        }
        .launch-title {
            font-size: 1.1rem;
        }
    }

    @media (max-width: 576px) {
        .new-launches-section {
            padding: 40px 0 !important;
        }
        
        .launch-image {
            height: 130px;
        }
        
        .launch-content {
            padding: 15px 12px;
        }
        .launch-title {
            font-size: 1rem;
            margin-bottom: 6px;
        }
        
        .launch-description {
            font-size: 0.85rem;
        }
    }

    /* More FAQs Button Styles */
    .btn-outline-primary:hover {
        background-color: #1DBF73 !important;
        border-color: #1DBF73 !important;
        color: white !important;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(29, 191, 115, 0.3);
    }

    .btn-outline-primary:focus {
        box-shadow: 0 0 0 0.2rem rgba(29, 191, 115, 0.25);
    }
</style>
@endsection
@section('content')
	<main>
		<!-- Notification Section -->
		@if(session('warning'))
			<div class="alert alert-warning alert-dismissible fade show" role="alert" style="margin: 0; border-radius: 0;">
				<div class="container">
					<strong>Notice:</strong> {{ session('warning') }}
					<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
				</div>
			</div>
		@endif
		
		<section class="yuktione-home-hero" aria-labelledby="yuktione-home-title">
			<div class="yuktione-home-hero__stage">
				<div class="yuktione-home-hero__copy">
					<h1 class="yuktione-home-hero__title" id="yuktione-home-title">
						<span>Real people.</span>
						<span>Practical guidance.</span>
						<em>Real progress.</em>
					</h1>
					<p class="yuktione-home-hero__description">Expert support, tools and programmes for your mental wellbeing, career, nutrition, finance and more.</p>

					<div class="yuktione-home-hero__search">
						<svg viewBox="0 0 24 24" aria-hidden="true" focusable="false">
							<circle cx="10.8" cy="10.8" r="6.8"></circle>
							<path d="m16 16 4.2 4.2"></path>
						</svg>
						<label class="visually-hidden" for="serviceSearch">Search experts, tools or topics</label>
						<input type="search" id="serviceSearch" placeholder="Search experts, tools or topics..." autocomplete="off">
						<button type="button" id="searchButton" aria-label="Search services">
							<svg viewBox="0 0 24 24" aria-hidden="true" focusable="false">
								<circle cx="10.8" cy="10.8" r="6.8"></circle>
								<path d="m16 16 4.2 4.2"></path>
							</svg>
						</button>
						<div id="searchResults" class="d-none" role="listbox" aria-label="Search results"></div>
					</div>
				</div>

				<div class="yuktione-home-hero__media">
					<img class="yuktione-home-hero__photo" src="{{ asset('frontend/assets/img/slides/slide_home_2.jpg') }}" alt="A smiling woman holding a cup in a bright indoor setting" fetchpriority="high">
					<div class="yuktione-home-hero__note" aria-hidden="true">A calmer,<br>happier you</div>
					<svg class="yuktione-home-hero__leaves" viewBox="0 0 240 210" aria-hidden="true" focusable="false">
						<path d="M111 207c-2-52 3-112 48-174" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round"/>
						<path d="M137 130c-47-4-69-31-72-75 39 9 65 33 72 75Z" fill="currentColor" opacity=".78"/>
						<path d="M144 106c-4-40 15-67 54-86 4 37-14 67-54 86Z" fill="currentColor" opacity=".92"/>
						<path d="M113 171c-48 1-79-19-95-60 42-1 76 18 95 60Z" fill="currentColor" opacity=".7"/>
						<path d="M119 164c6-40 31-63 75-70-8 39-32 64-75 70Z" fill="currentColor" opacity=".86"/>
						<path d="M111 202c-39 11-71 1-98-31 35-12 69-2 98 31Z" fill="currentColor" opacity=".68"/>
					</svg>
					<a class="yuktione-home-hero__journey" href="{{ route('register') }}">
						<span class="yuktione-home-hero__journey-icon" aria-hidden="true">
							<svg viewBox="0 0 24 24" focusable="false"><path d="M12 21s-7.5-4.5-7.5-11a4.2 4.2 0 0 1 7.5-2.6A4.2 4.2 0 0 1 19.5 10c0 6.5-7.5 11-7.5 11Z"/><path d="M12 6c.7-2 2.2-3 4.5-3"/></svg>
						</span>
						<span>Your journey to a brighter you starts here.</span>
						<span class="yuktione-home-hero__journey-arrow" aria-hidden="true">
							<svg viewBox="0 0 24 24" focusable="false"><path d="M4 12h15M13 6l6 6-6 6"/></svg>
						</span>
					</a>
				</div>
			</div>

			@if ($services->isNotEmpty())
				<nav class="yuktione-home-hero__services" aria-label="Explore services">
					@foreach ($services->take(5) as $service)
						<a class="yuktione-home-hero__service" href="{{ url('/service/' . $service->slug) }}">
							<span class="yuktione-home-hero__service-icon" aria-hidden="true">
								<svg viewBox="0 0 40 40" focusable="false">
									<path d="M20 34V15M20 23c-8 0-12-4-12-11 7 0 12 4 12 11Zm0-4c0-7 4-11 12-11 0 7-4 11-12 11Z"/>
								</svg>
							</span>
							<span>{{ $service->name }}</span>
							<span class="yuktione-home-hero__service-arrow" aria-hidden="true">
								<svg viewBox="0 0 24 24" focusable="false"><path d="M5 12h14m-6-6 6 6-6 6"/></svg>
							</span>
						</a>
					@endforeach
				</nav>
			@endif
		</section>

		@if($homepageCategories->isNotEmpty())
			<section class="homepage-category-slider" aria-label="Browse service categories">
				<div class="homepage-category-slider__inner">
					<div class="homepage-category-slider__heading">
						<h2>Explore categories</h2>
						<div class="homepage-category-slider__controls">
							<button type="button" class="homepage-category-slider__arrow" data-category-slide-prev aria-label="Scroll to previous categories" disabled>
								<svg viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="m15 18-6-6 6-6"/></svg>
							</button>
							<button type="button" class="homepage-category-slider__arrow" data-category-slide-next aria-label="Scroll to next categories">
								<svg viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="m9 18 6-6-6-6"/></svg>
							</button>
						</div>
					</div>
					<div class="homepage-category-slider__track" data-category-slider-track tabindex="0" aria-label="Service categories; use left and right arrow keys to scroll">
						@foreach($homepageCategories as $categoryBox)
							@php
								$categoryService = $categoryBox->subCategories->first()?->service;
								$categoryUrl = route('gridlisting', $categoryService ? ['service' => $categoryService->slug] : []);
							@endphp
							<a class="homepage-category-card" href="{{ $categoryUrl }}" aria-label="Explore {{ $categoryBox->name }}">
								<span class="homepage-category-card__icon" aria-hidden="true">
									@if($categoryBox->icon_path)
										<img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($categoryBox->icon_path) }}" alt="" loading="lazy">
									@elseif($categoryBox->icon_name)
										<i class="{{ $categoryBox->icon_name }}"></i>
									@else
										<svg viewBox="0 0 24 24" focusable="false"><path d="M12 3v18M3 12h18M5.6 5.6l12.8 12.8m0-12.8L5.6 18.4"/></svg>
									@endif
								</span>
								<span class="homepage-category-card__name">{{ $categoryBox->name }}</span>
								<span class="homepage-category-card__arrow" aria-hidden="true">
									<svg viewBox="0 0 24 24" focusable="false"><path d="M5 12h14m-6-6 6 6-6 6"/></svg>
								</span>
							</a>
						@endforeach
					</div>
				</div>
			</section>
		@endif
		
		<!-- Popular Services Section -->
		<section class="popular-services-main-section py-5" style="background-color: white;">
			<div class="container">
				<div class="row">
					<div class="col-12">
						<h2 class="popular-services-title">Popular services</h2>
						<div class="popular-services-scroll-container">
							<div class="popular-services-wrapper" id="popularServicesScroll">
								@foreach($services as $service)
								<!-- Service Card: {{ $service->name }} -->
								<a href="{{ route('service.show', $service->slug) }}" class="popular-service-card">
									<div class="service-content">
										<h3 class="service-title">{{ $service->name }}</h3>
									</div>
									<div class="service-graphic">
										<div class="service-mini-card">
											<img src="{{ $service->image ? asset('storage/' . $service->image) : asset('frontend/assets/img/new-icons/header-menu-icon/designer.png') }}" alt="{{ $service->name }}" class="service-image">
										</div>
									</div>
								</a>
								@endforeach
							</div>
							
							<button class="scroll-arrow-right" onclick="scrollPopularServices('right')">
								<i class="fas fa-chevron-right"></i>
							</button>
						</div>
					</div>
				</div>
			</div>
		</section>
		
        
		<!-- Tazen Pro Section - Compact & Stylish -->
		<section class="tazen-pro-section-compact py-4" style="background: white;">
			<div class="container" style="max-width: 1200px;">
				<div class="pro-compact-card">
					<div class="row align-items-center g-4">
						
						<div class="col-lg-4">
							<div class="pro-branding">
								<div class="pro-logo-compact mb-3">
									<span class="brand-name-compact">tazen</span>
									{{-- <span class="pro-badge">PRO</span> --}}
								</div>
								<p class="pro-subtitle">Elevate with experts – One platform multiple solutions</p>
								<button class="btn-pro-cta">
									<span>Explore</span>
									<i class="fas fa-arrow-right ms-2"></i>
								</button>
							</div>
						</div>
						
						<!-- Right Side - Feature Grid -->
						<div class="col-lg-8">
							<div class="pro-features-grid">
								<div class="pro-feature-card">
									<div class="feature-icon-box">
										<i class="fas fa-user-tie"></i>
									</div>
									<div class="feature-text">
										<h5>Connect with verified experts </h5>
										<p>Our experts simplify your problems</p>
									</div>
								</div>
								
								<div class="pro-feature-card">
									<div class="feature-icon-box">
										<i class="fas fa-shield-alt"></i>
									</div>
									<div class="feature-text">
										<h5>Get customised solutions</h5>
										<p>Speak to our RM and get customised solutions</p>
									</div>
								</div>
								
								<div class="pro-feature-card">
									<div class="feature-icon-box">
										<i class="fas fa-tools"></i>
									</div>
									<div class="feature-text">
										<h5>Advanced Dashboard</h5>
										<p>Track all your appointments through our smart dashboard</p>
									</div>
								</div>
								
								<div class="pro-feature-card">
									<div class="feature-icon-box">
										<i class="fas fa-wallet"></i>
									</div>
									<div class="feature-text">
										<h5>Access to events and blogs</h5>
										<p>Stay updated through our events and blogs</p>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</section>


		<!-- second popups  -->

		


		<!-- Why Choose Us Section - New Design -->
		<section class="why-choose-section py-4">
			<div class="container" style="width: 85%; max-width: 1200px; margin: 0 auto;">
				@foreach ($whychooses as $whychoose)
				<div class="why-choose-container">
					<div class="why-choose-left">
						<h2 class="why-choose-title">{{ $whychoose->section_heading }}</h2>
						<p class="why-choose-description">{{ $whychoose->section_sub_heading }}</p>
						
						<div class="image-grid">
							<div class="image-item">
								<img src="https://images.unsplash.com/photo-1556909114-f6e7ad7d3136?w=300&h=300&fit=crop&crop=center" alt="Professional Service 1" class="img-fluid">
							</div>
							<div class="image-item">
								<img src="https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=300&h=300&fit=crop&crop=center" alt="Professional Service 2" class="img-fluid">
							</div>
							<div class="image-item">
								<img src="https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?w=300&h=300&fit=crop&crop=center" alt="Professional Service 3" class="img-fluid">
							</div>
							<div class="image-item">
								<img src="https://images.unsplash.com/photo-1519085360753-af0119f7cbe7?w=300&h=300&fit=crop&crop=center" alt="Professional Service 4" class="img-fluid">
							</div>
						</div>
					</div>
					
					<div class="why-choose-right">
						<div class="reason-item">
							<div class="reason-number">01</div>
							<div class="reason-content">
								<h3 class="reason-title">{{ $whychoose->card1_heading }}</h3>
								<p class="reason-description">{{ $whychoose->card1_description }}</p>
							</div>
						</div>
						
						<div class="reason-item">
							<div class="reason-number">02</div>
							<div class="reason-content">
								<h3 class="reason-title">{{ $whychoose->card2_heading }}</h3>
								<p class="reason-description">{{ $whychoose->card2_description }}</p>
							</div>
						</div>
						
						<div class="reason-item">
							<div class="reason-number">03</div>
							<div class="reason-content">
								<h3 class="reason-title">{{ $whychoose->card3_heading }}</h3>
								<p class="reason-description">{{ $whychoose->card3_description }}</p>
							</div>
						</div>
						
						<div class="reason-item">
							<div class="reason-number">04</div>
							<div class="reason-content">
								<h3 class="reason-title">{{ $whychoose->card4_heading }}</h3>
								<p class="reason-description">{{ $whychoose->card4_description }}</p>
							</div>
						</div>
					</div>
				</div>
				@endforeach
			</div>
		</section>
		<!-- end Why Choose Us Section -->


		<!-- /bg_gray -->

		@include('frontend.home.partials.events')
		@include('frontend.home.partials.support-cta')
		@include('frontend.home.partials.how-it-works')

		@include('frontend.home.partials.testimonials')
		@include('frontend.home.partials.benefits')

		@include('frontend.home.partials.blogs')
		@include('frontend.home.partials.faq')
		@include('frontend.home.partials.professional-cta')

	</main>

    @endsection
	@section('script')
	<script>
// Simple service selection function for index page
function selectServiceAndRedirect(serviceId, serviceName) {
    // Save service ID and name in session for later use
    fetch('/set-service-session', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({
            service_id: serviceId,
            service_name: serviceName
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            // Redirect to professionals listing page
            window.location.href = '/professionals';
        } else {
            console.error('Failed to save service session');
            // Even if session save fails, still redirect
            window.location.href = '/professionals';
        }
    })
    .catch(error => {
        console.error('Error:', error);
        // Even if session save fails, still redirect
        window.location.href = '/professionals';
    });
}
</script>
		
	<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
	
	<script>
		// Popular Services scroll functionality
		function scrollPopularServices(direction) {
			const scrollContainer = document.querySelector('#popularServicesScroll');
			const scrollAmount = 300;
			
			if (direction === 'right') {
				scrollContainer.scrollBy({
					left: scrollAmount,
					behavior: 'smooth'
				});
			} else if (direction === 'left') {
				scrollContainer.scrollBy({
					left: -scrollAmount,
					behavior: 'smooth'
				});
			}
		}

		// Services scroll functionality
		function scrollServices(direction) {
			const scrollContainer = document.querySelector('#servicesScroll');
			const scrollAmount = 300;
			
			if (direction === 'right') {
				scrollContainer.scrollBy({
					left: scrollAmount,
					behavior: 'smooth'
				});
			} else if (direction === 'left') {
				scrollContainer.scrollBy({
					left: -scrollAmount,
					behavior: 'smooth'
				});
			}
		}

		// Auto scroll functionality for services with infinite loop
		function autoScrollServices() {
			const scrollContainer = document.querySelector('#servicesScroll');
			if (!scrollContainer) return;
			
			const scrollAmount = 220; // Width of one service card + gap
			const halfWidth = scrollContainer.scrollWidth / 2; // Half of total width (since we duplicated)
			
			// If we've scrolled past the first set, reset to beginning seamlessly
			if (scrollContainer.scrollLeft >= halfWidth - 10) {
				scrollContainer.scrollTo({
					left: 0,
					behavior: 'auto' // Instant reset for seamless loop
				});
			} else {
				scrollContainer.scrollBy({
					left: scrollAmount,
					behavior: 'smooth'
				});
			}
		}

		// Start auto scroll when page loads
		document.addEventListener('DOMContentLoaded', function() {
			// Auto scroll every 3 seconds
			setInterval(autoScrollServices, 3000);
		});
	</script>
 @endsection


<style>
.carousel-arrow {
    transition: background 0.2s;
}
.carousel-arrow:hover {
    background: #f0f0f0;
}
@media (max-width: 991px) {
    .carousel-arrow.left-arrow { left: 0; }
    .carousel-arrow.right-arrow { right: 0; }
}

</style>
<script>
document.addEventListener('DOMContentLoaded', function() {
    var $carousel = $('.services-carousal');
    $('.left-arrow').on('click', function() {
        $carousel.trigger('prev.owl.carousel');
    });
    $('.right-arrow').on('click', function() {
        $carousel.trigger('next.owl.carousel');
    });
});
</script>
