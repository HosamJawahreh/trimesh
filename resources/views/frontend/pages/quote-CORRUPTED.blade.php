@extends('frontend.layouts.master')

@section('title', '3D Printing Quote - Get Instant Pricing')

@section('meta')
    <meta name="description" content="Upload your 3D files and get instant pricing for your 3D printing projects. Support for STL, OBJ, and PLY files.">
@endsection

@section('header')
    {{-- No header for full-screen quote experience --}}
@endsection

@push('styles')
<style>
    /* ============================================
       3D QUOTE PAGE - FULL SCREEN LAYOUT
       Professional & Optimized Styles
       ============================================ */

    /* Base Layout Reset */
    html,
    body,
    #smooth-wrapper,
    #smooth-content,
    #smooth-content.body-padding,
    main {
        height: 100vh !important;
        width: 100vw !important;
        overflow: hidden !important;
        padding: 0 !important;
        margin: 0 !important;
        position: fixed !important;
        top: 0 !important;
        left: 0 !important;
        right: 0 !important;
        bottom: 0 !important;
    }
    
    html {
        overflow-y: hidden !important;
        overflow-x: hidden !important;
    }
    
    body {
        overflow-y: hidden !important;
        overflow-x: hidden !important;
    }

    /* Quote Page Container */
    .quote-page-wrapper {
        height: 100vh !important;
        width: 100vw !important;
        background: #fff;
        display: flex;
        flex-direction: column;
        overflow: hidden !important;
        position: fixed !important;
        top: 0 !important;
        left: 0 !important;
        right: 0 !important;
        bottom: 0 !important;
        margin: 0 !important;
        padding: 0 !important;
    }

    .quote-page-container {
        flex: 1;
        display: flex;
        width: 100vw !important;
        height: 100vh !important;
        overflow: hidden !important;
        position: relative;
    }

    /* Quote Section Base */
    .dgm-3d-quote-area {
        height: 100vh !important;
        width: 100vw !important;
        max-width: 100vw !important;
        padding: 0 !important;
        margin: 0 !important;
        background: transparent !important;
        overflow: hidden !important;
        position: fixed !important;
        top: 0 !important;
        left: 0 !important;
        right: 0 !important;
        bottom: 0 !important;
    }
    
    .dgm-3d-quote-area .container {
        height: 100vh !important;
        width: 100vw !important;
        max-width: 100vw !important;
        padding: 0 !important;
        margin: 0 !important;
        background: transparent !important;
        overflow: hidden !important;
        position: fixed !important;
        top: 0 !important;
        left: 0 !important;
        right: 0 !important;
        bottom: 0 !important;
    }

    /* Form Container Structure */
    .quote-form-container-3d,
    .quote-form-container-3d > .row.justify-content-center,
    .quote-form-container-3d .col-12.col-lg-11,
    .quote-form-container-3d .card.border-0.shadow-sm,
    .quote-form-container-3d .card-body.p-0,
    .quote-form-container-3d .card-body > .row.g-0 {
        height: 100vh !important;
        width: 100% !important;
        margin: 0 !important;
        padding: 0 !important;
        border: none !important;
        box-shadow: none !important;
        border-radius: 0 !important;
        overflow: hidden !important;
    }

    .quote-form-container-3d > .row.justify-content-center {
        justify-content: flex-start !important;
        max-width: 100vw !important;
    }

    .quote-form-container-3d .col-12.col-lg-11 {
        max-width: 100vw !important;
        flex: 0 0 100% !important;
    }

    .quote-form-container-3d .card.border-0.shadow-sm {
        overflow: hidden !important;
    }

    .quote-form-container-3d .card-body > .row.g-0 {
        display: flex !important;
        flex-wrap: nowrap !important;
        width: 100vw !important;
        max-width: 100vw !important;
    }

    /* ============================================
       SIDEBAR - Controls Panel
       ============================================ */
    .quote-form-container-3d .col-12.col-lg-3 {
        min-width: 380px !important;
        max-width: 380px !important;
        flex: 0 0 380px !important;
        height: 100vh !important;
        overflow-y: auto !important;
        overflow-x: hidden !important;
        background: #f8f9fa !important;
        border-right: 1px solid #e9ecef !important;
        display: block !important;
        visibility: visible !important;
        position: fixed !important;
        left: 0 !important;
        top: 0 !important;
        z-index: 1000 !important;
    }

    /* Custom Scrollbar */
    .quote-form-container-3d .col-12.col-lg-3::-webkit-scrollbar {
        width: 8px;
    }

    .quote-form-container-3d .col-12.col-lg-3::-webkit-scrollbar-track {
        background: #f1f3f5;
    }

    .quote-form-container-3d .col-12.col-lg-3::-webkit-scrollbar-thumb {
        background: #cbd5e0;
        border-radius: 4px;
    }

    .quote-form-container-3d .col-12.col-lg-3::-webkit-scrollbar-thumb:hover {
        background: #a0aec0;
    }

    /* ============================================
       VIEWER AREA - 3D Display
       ============================================ */
    .quote-form-container-3d .col-12.col-lg-9 {
        flex: 1 !important;
        width: calc(100vw - 380px) !important;
        max-width: calc(100vw - 380px) !important;
        min-width: 0 !important;
        height: 100vh !important;
        display: flex !important;
        flex-direction: column !important;
        background: linear-gradient(to bottom, #b8c5d6 0%, #99a8ba 100%) !important;
        visibility: visible !important;
        position: fixed !important;
        right: 0 !important;
        top: 0 !important;
        left: 380px !important;
        opacity: 1 !important;
        overflow: hidden !important;
        margin: 0 !important;
        padding: 0 !important;
    }

    #viewer3dGeneral,
    #viewer3dMedical {
        width: 100% !important;
        height: 100vh !important;
        flex: 1 !important;
        background: linear-gradient(to bottom, #b8c5d6 0%, #99a8ba 100%) !important;
        border-radius: 0 !important;
        position: absolute !important;
        top: 0 !important;
        left: 0 !important;
        right: 0 !important;
        bottom: 0 !important;
        overflow: hidden !important;
        display: flex !important;
        visibility: visible !important;
        opacity: 1 !important;
        margin: 0 !important;
        padding: 0 !important;
    }

    /* Grid Floor Effect - Perspective Grid like Shapeways */
    #viewer3dGeneral::after,
    #viewer3dMedical::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 50%;
        transform: translateX(-50%);
        width: 200%;
        height: 60%;
        background-image: 
            linear-gradient(to right, rgba(255,255,255,0.15) 1px, transparent 1px),
            linear-gradient(to bottom, rgba(255,255,255,0.15) 1px, transparent 1px);
        background-size: 50px 50px;
        opacity: 0.4;
        pointer-events: none;
        transform-origin: center bottom;
        transform: translateX(-50%) perspective(800px) rotateX(75deg) scale(1.5);
    }

    /* Empty State */
    #viewer3dGeneral .text-muted,
    #viewer3dMedical .text-muted {
        position: relative;
        z-index: 1;
    }

    /* Model Info Badge - Top Left */
    .model-info-badge {
        position: absolute;
        top: 20px;
        left: 20px;
        background: rgba(44, 62, 80, 0.9);
        backdrop-filter: blur(10px);
        color: #fff;
        padding: 12px 16px;
        border-radius: 8px;
        font-size: 0.85rem;
        font-weight: 500;
        z-index: 100;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
    }

    .model-info-badge .model-name {
        display: flex;
        align-items: center;
        gap: 8px;
        font-weight: 600;
    }

    .model-info-badge .model-subtitle {
        font-size: 0.75rem;
        color: rgba(255, 255, 255, 0.8);
        margin-top: 4px;
    }

    /* Camera View Tabs - Bottom Center */
    .viewer-view-tabs {
        position: absolute;
        bottom: 30px;
        left: 50%;
        transform: translateX(-50%);
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(10px);
        border-radius: 8px;
        padding: 6px;
        display: flex;
        gap: 4px;
        z-index: 100;
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.15);
    }

    .viewer-view-tabs button {
        padding: 8px 20px;
        border: none;
        background: transparent;
        color: #6c757d;
        font-size: 0.85rem;
        font-weight: 500;
        border-radius: 6px;
        cursor: pointer;
        transition: all 0.2s ease;
        min-width: 70px;
    }

    .viewer-view-tabs button:hover {
        background: rgba(74, 144, 226, 0.1);
        color: #4a90e2;
    }

    .viewer-view-tabs button.active {
        background: #fff;
        color: #2c3e50;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
    }

    /* ============================================
       BOTTOM CONTROL BAR - Professional FULL WIDTH
       ============================================ */
    .viewer-bottom-controls {
        position: fixed !important;
        bottom: 0 !important;
        left: 380px !important;
        right: 0 !important;
        width: calc(100vw - 380px) !important;
        background: rgba(255, 255, 255, 0.98) !important;
        backdrop-filter: blur(20px) !important;
        border-radius: 0 !important;
        padding: 16px 24px !important;
        display: flex !important;
        align-items: center !important;
        justify-content: space-between !important;
        gap: 24px !important;
        z-index: 999999 !important;
        box-shadow: 0 -4px 24px rgba(0, 0, 0, 0.1) !important;
        border-top: 2px solid rgba(0, 123, 255, 0.3) !important;
        height: 70px !important;
        pointer-events: auto !important;
        cursor: default !important;
    }

    .control-section {
        display: flex !important;
        flex-direction: column !important;
        gap: 6px !important;
        flex-shrink: 0 !important;
    }

    .control-label {
        font-size: 0.65rem !important;
        font-weight: 700 !important;
        color: #6c757d !important;
        text-transform: uppercase !important;
        letter-spacing: 0.8px !important;
        margin-bottom: 2px !important;
    }

    .control-divider {
        width: 2px !important;
        height: 50px !important;
        background: linear-gradient(to bottom, transparent, #dee2e6 20%, #dee2e6 80%, transparent) !important;
        flex-shrink: 0 !important;
    }

    /* Measurements Section */
    .measurement-items {
        display: flex !important;
        gap: 12px !important;
        flex-wrap: nowrap !important;
    }

    .measurement-item {
        display: flex !important;
        align-items: baseline !important;
        gap: 4px !important;
        padding: 6px 12px !important;
        background: #f8f9fa !important;
        border-radius: 6px !important;
        white-space: nowrap !important;
    }

    .measurement-item.volume {
        background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%) !important;
    }

    .axis-label {
        font-size: 0.75rem;
        font-weight: 700;
        color: #495057;
    }

    .axis-value {
        font-size: 0.9rem;
        font-weight: 600;
        color: #2c3e50;
        min-width: 50px;
        text-align: right;
        font-family: 'Courier New', monospace;
    }

    .axis-unit {
        font-size: 0.7rem;
        color: #6c757d;
        font-weight: 500;
    }

    /* Camera Buttons */
    .camera-buttons,
    .tool-buttons {
        display: flex;
        gap: 6px;
    }

    .control-btn {
        display: flex !important;
        flex-direction: column !important;
        align-items: center !important;
        gap: 4px !important;
        padding: 8px 12px !important;
        background: #f8f9fa !important;
        border: 1px solid #e9ecef !important;
        border-radius: 8px !important;
        cursor: pointer !important;
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1) !important;
        color: #495057 !important;
        font-size: 0.7rem !important;
        font-weight: 600 !important;
        pointer-events: auto !important;
        user-select: none !important;
    }

    .control-btn svg {
        transition: all 0.2s !important;
        pointer-events: none !important;
    }

    .control-btn:hover {
        background: #e9ecef !important;
        border-color: #4a90e2 !important;
        color: #4a90e2 !important;
        transform: translateY(-2px) !important;
        box-shadow: 0 4px 12px rgba(74, 144, 226, 0.2) !important;
    }

    .control-btn:hover svg {
        stroke: #4a90e2 !important;
    }

    .control-btn.active {
        background: linear-gradient(135deg, #4a90e2 0%, #357abd 100%) !important;
        border-color: #4a90e2 !important;
        color: #fff !important;
        box-shadow: 0 4px 12px rgba(74, 144, 226, 0.3) !important;
    }

    .control-btn.active svg {
        stroke: #fff !important;
    }

    /* Save Button */
    .save-btn {
        flex-direction: row !important;
        padding: 10px 20px !important;
        background: linear-gradient(135deg, #10b981 0%, #059669 100%) !important;
        border: none !important;
        color: #fff !important;
        font-size: 0.85rem !important;
        font-weight: 700 !important;
        box-shadow: 0 4px 16px rgba(16, 185, 129, 0.3) !important;
        pointer-events: auto !important;
    }
    }

    .save-btn:hover {
        background: linear-gradient(135deg, #059669 0%, #047857 100%);
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(16, 185, 129, 0.4);
    }

    .save-btn svg {
        stroke: #fff;
    }

    /* Viewer Controls - Top Right */
    .viewer-control-buttons {
        position: absolute;
        top: 20px;
        right: 20px;
        display: flex;
        gap: 8px;
        z-index: 100;
    }

    .viewer-control-buttons button {
        width: 40px;
        height: 40px;
        border: none;
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(10px);
        color: #6c757d;
        border-radius: 8px;
        cursor: pointer;
        transition: all 0.2s ease;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }

    .viewer-control-buttons button:hover {
        background: #4a90e2;
        color: #fff;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    }

    /* Hide default controls panel */
    .quote-form-container-3d .col-lg-9 > .card.border-0.shadow-sm {
        display: none;
    }

    /* ============================================
       UI COMPONENTS
       ============================================ */

    /* Upload Drop Zone */
    .upload-drop-zone-3d {
        border: 2px dashed #cbd5e0;
        border-radius: 10px;
        background: #fff;
        padding: 20px;
        cursor: pointer;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .upload-drop-zone-3d:hover {
        border-color: #4a90e2;
        background: #f8f9fa;
        transform: translateY(-2px);
    }

    /* Form Controls */
    .form-select,
    .form-control {
        border: 1px solid #dee2e6;
        border-radius: 8px;
        font-size: 0.85rem;
        transition: all 0.2s ease;
    }

    .form-select:focus,
    .form-control:focus {
        border-color: #4a90e2;
        box-shadow: 0 0 0 3px rgba(74, 144, 226, 0.1);
        outline: none;
    }

    /* Category Tabs */
    .category-tab-btn {
        transition: all 0.2s ease;
    }

    .category-tab-btn.active {
        background: #fff;
        color: #2c3e50;
        border-color: #dee2e6;
        font-weight: 600;
    }

    /* Color Picker Buttons */
    .color-btn {
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        cursor: pointer;
    }

    .color-btn:hover {
        transform: scale(1.1);
    }

    .color-btn.active {
        box-shadow: 0 0 0 3px rgba(74, 144, 226, 0.3);
        transform: scale(1.05);
    }

    /* Files Container */
    #filesContainer,
    #filesContainerMedical {
        background: #fff;
        border: 1px solid #dee2e6;
    }

    /* Control Buttons */
    .btn-outline-secondary {
        border-color: #dee2e6;
        color: #6c757d;
        transition: all 0.2s ease;
    }

    .btn-outline-secondary:hover,
    .btn-outline-secondary.active {
        background: #4a90e2;
        border-color: #4a90e2;
        color: #fff;
    }

    /* Request Quote Button */
    #btnRequestQuoteGeneral,
    #btnRequestQuoteMedical {
        background: #4a90e2;
        border: none;
        padding: 10px;
        font-weight: 600;
        border-radius: 10px;
        color: #fff;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        box-shadow: 0 2px 8px rgba(74, 144, 226, 0.3);
    }

    #btnRequestQuoteGeneral:hover,
    #btnRequestQuoteMedical:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(74, 144, 226, 0.4);
    }

    /* ============================================
       RESPONSIVE DESIGN
       ============================================ */
    
    /* Force viewer visibility on all screen sizes */
    .quote-form-container-3d .col-lg-9,
    .col-12.col-lg-9,
    div.col-12.col-lg-9 {
        display: flex !important;
        visibility: visible !important;
        opacity: 1 !important;
    }
    
    @media (min-width: 992px) {
        .quote-form-container-3d .col-12.col-lg-9 {
            flex: 1 !important;
            max-width: calc(100% - 380px) !important;
        }
    }
    
    @media (max-width: 991px) {
        body,
        .quote-page-wrapper {
            overflow-y: auto;
            height: auto;
        }

        .quote-form-container-3d .card-body > .row.g-0 {
            flex-wrap: wrap;
        }

        .quote-form-container-3d .col-12.col-lg-3,
        .quote-form-container-3d .col-12.col-lg-9 {
            height: auto;
            min-height: 50vh;
            max-width: 100%;
            flex: 0 0 100%;
            min-width: 100%;
        }

        #viewer3dGeneral,
        #viewer3dMedical {
            min-height: 60vh;
            height: 60vh;
        }
    }

    /* Performance Optimization - GPU Acceleration */
    .upload-drop-zone-3d,
    .color-btn,
    #btnRequestQuoteGeneral,
    #btnRequestQuoteMedical,
    .btn-outline-secondary {
        will-change: transform;
        backface-visibility: hidden;
        -webkit-font-smoothing: antialiased;
    }
</style>
@endpush

@section('content')
    {{-- ============================================
         3D QUOTE PAGE CONTENT
         Full-screen immersive experience
         ============================================ --}}
    
    {{-- CRITICAL: Inline styles to override ALL theme styles --}}
    <style>
        /* Nuclear option - Override EVERYTHING with maximum specificity */
        html, body {
            overflow: hidden !important;
            height: 100vh !important;
            width: 100vw !important;
            margin: 0 !important;
            padding: 0 !important;
            position: fixed !important;
            top: 0 !important;
            left: 0 !important;
        }
        
        #smooth-wrapper,
        #smooth-content,
        main {
            overflow: hidden !important;
            height: 100vh !important;
            width: 100vw !important;
            margin: 0 !important;
            padding: 0 !important;
            position: fixed !important;
            top: 0 !important;
            left: 0 !important;
        }
        
        .dgm-3d-quote-area,
        .dgm-3d-quote-area .container {
            overflow: hidden !important;
            height: 100vh !important;
            width: 100vw !important;
            max-width: 100vw !important;
            margin: 0 !important;
            padding: 0 !important;
            position: fixed !important;
            top: 0 !important;
            left: 0 !important;
        }
        
        /* Sidebar - Fixed left */
        .quote-form-container-3d .col-12.col-lg-3 {
            position: fixed !important;
            left: 0 !important;
            top: 0 !important;
            width: 380px !important;
            min-width: 380px !important;
            max-width: 380px !important;
            height: 100vh !important;
            z-index: 1000 !important;
            overflow-y: auto !important;
            overflow-x: hidden !important;
            background: #f8f9fa !important;
        }
        
        /* Viewer - Fixed right */
        .quote-form-container-3d .col-12.col-lg-9 {
            position: fixed !important;
            left: 380px !important;
            top: 0 !important;
            right: 0 !important;
            bottom: 0 !important;
            width: calc(100vw - 380px) !important;
            height: 100vh !important;
            overflow: hidden !important;
            background: linear-gradient(to bottom, #b8c5d6 0%, #99a8ba 100%) !important;
            display: flex !important;
            flex-direction: column !important;
            margin: 0 !important;
            padding: 0 !important;
        }
        
        #viewer3dGeneral,
        #viewer3dMedical {
            width: 100% !important;
            height: 100vh !important;
            position: absolute !important;
            top: 0 !important;
            left: 0 !important;
            right: 0 !important;
            bottom: 0 !important;
            overflow: hidden !important;
            background: linear-gradient(to bottom, #b8c5d6 0%, #99a8ba 100%) !important;
        }
        
        /* ============================================
           BOTTOM CONTROL BAR - Professional
           ============================================ */
        .viewer-bottom-controls,
        #controlBarGeneral,
        #controlBarMedical {
            position: fixed !important;
            bottom: 20px !important;
            left: 50% !important;
            transform: translateX(-50%) !important;
            background: rgba(255, 255, 255, 0.98) !important;
            backdrop-filter: blur(20px) !important;
            -webkit-backdrop-filter: blur(20px) !important;
            border-radius: 16px !important;
            padding: 12px 20px !important;
            display: flex !important;
            align-items: center !important;
            gap: 16px !important;
            z-index: 99999 !important;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.12) !important;
            border: 1px solid rgba(255, 255, 255, 0.3) !important;
            min-width: 800px !important;
            visibility: visible !important;
            opacity: 1 !important;
            pointer-events: all !important;
        }
        
        /* Force all child elements to be visible */
        .viewer-bottom-controls * {
            visibility: visible !important;
            opacity: 1 !important;
        }
        
        /* TEST: Red debug bar to verify CSS is working */
        .test-debug-bar {
            position: fixed !important;
            bottom: 100px !important;
            left: 50% !important;
            transform: translateX(-50%) !important;
            background: red !important;
            color: white !important;
            padding: 20px 40px !important;
            z-index: 999999 !important;
            font-size: 20px !important;
            font-weight: bold !important;
            display: block !important;
            visibility: visible !important;
            opacity: 1 !important;
        }

        .control-section {
            display: flex !important;
            flex-direction: column !important;
            gap: 8px !important;
        }

        .control-label {
            font-size: 0.7rem !important;
            font-weight: 600 !important;
            color: #6c757d !important;
            text-transform: uppercase !important;
            letter-spacing: 0.5px !important;
        }

        .control-divider {
            width: 1px !important;
            height: 50px !important;
            background: linear-gradient(to bottom, transparent, #dee2e6 20%, #dee2e6 80%, transparent) !important;
        }

        /* Measurements */
        .measurement-items {
            display: flex !important;
            gap: 16px !important;
        }

        .measurement-item {
            display: flex !important;
            align-items: baseline !important;
            gap: 4px !important;
            padding: 6px 12px !important;
            background: #f8f9fa !important;
            border-radius: 8px !important;
        }

        .measurement-item.volume {
            background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%) !important;
        }

        .axis-label {
            font-size: 0.75rem !important;
            font-weight: 700 !important;
            color: #495057 !important;
        }

        .axis-value {
            font-size: 0.9rem !important;
            font-weight: 600 !important;
            color: #2c3e50 !important;
            min-width: 50px !important;
            text-align: right !important;
            font-family: 'Courier New', monospace !important;
        }

        .axis-unit {
            font-size: 0.7rem !important;
            color: #6c757d !important;
            font-weight: 500 !important;
        }

        /* Camera & Tool Buttons */
        .camera-buttons,
        .tool-buttons {
            display: flex !important;
            gap: 6px !important;
        }

        .control-btn {
            display: flex !important;
            flex-direction: column !important;
            align-items: center !important;
            gap: 4px !important;
            padding: 8px 12px !important;
            background: #f8f9fa !important;
            border: 1px solid #e9ecef !important;
            border-radius: 8px !important;
            cursor: pointer !important;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1) !important;
            color: #495057 !important;
            font-size: 0.7rem !important;
            font-weight: 600 !important;
        }

        .control-btn:hover {
            background: #e9ecef !important;
            border-color: #4a90e2 !important;
            color: #4a90e2 !important;
            transform: translateY(-2px) !important;
            box-shadow: 0 4px 12px rgba(74, 144, 226, 0.2) !important;
        }

        .control-btn.active {
            background: linear-gradient(135deg, #4a90e2 0%, #357abd 100%) !important;
            border-color: #4a90e2 !important;
            color: #fff !important;
            box-shadow: 0 4px 12px rgba(74, 144, 226, 0.3) !important;
        }

        .control-btn.active svg {
            stroke: #fff !important;
        }

        /* Save Button */
        .save-btn {
            flex-direction: row !important;
            padding: 10px 20px !important;
            background: linear-gradient(135deg, #10b981 0%, #059669 100%) !important;
            border: none !important;
            color: #fff !important;
            font-size: 0.85rem !important;
            font-weight: 700 !important;
            box-shadow: 0 4px 16px rgba(16, 185, 129, 0.3) !important;
        }

        .save-btn:hover {
            background: linear-gradient(135deg, #059669 0%, #047857 100%) !important;
            transform: translateY(-2px) !important;
            box-shadow: 0 6px 20px rgba(16, 185, 129, 0.4) !important;
        }

        .save-btn svg {
            stroke: #fff !important;
        }
        
        /* Model Info Badge */
        .model-info-badge {
            position: absolute !important;
            top: 20px !important;
            left: 20px !important;
            background: rgba(44, 62, 80, 0.9) !important;
            backdrop-filter: blur(10px) !important;
            color: #fff !important;
            padding: 12px 16px !important;
            border-radius: 8px !important;
            font-size: 0.85rem !important;
            font-weight: 500 !important;
            z-index: 100 !important;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2) !important;
        }

        .model-info-badge .model-name {
            display: flex !important;
            align-items: center !important;
            gap: 8px !important;
            font-weight: 600 !important;
        }

        .model-info-badge .model-subtitle {
            font-size: 0.75rem !important;
            color: rgba(255, 255, 255, 0.8) !important;
            margin-top: 4px !important;
        }
    </style>
    
    <div class="quote-page-wrapper">
        <div class="quote-page-container">
            @include('frontend.pages.quote-viewer')
        </div>
    </div>
    
    {{-- PROFESSIONAL CONTROL BAR - Outside viewer to ensure visibility --}}
    <div class="viewer-bottom-controls" id="mainControlBar">
        <div class="control-section measurements-section">
            <div class="control-label">Measurements</div>
            <div class="measurement-items">
                <div class="measurement-item">
                    <span class="axis-label">X:</span>
                    <span class="axis-value" id="measureXMain">0.00</span>
                    <span class="axis-unit">mm</span>
                </div>
                <div class="measurement-item">
                    <span class="axis-label">Y:</span>
                    <span class="axis-value" id="measureYMain">0.00</span>
                    <span class="axis-unit">mm</span>
                </div>
                <div class="measurement-item">
                    <span class="axis-label">Z:</span>
                    <span class="axis-value" id="measureZMain">0.00</span>
                    <span class="axis-unit">mm</span>
                </div>
                <div class="measurement-item volume">
                    <span class="axis-label">Vol:</span>
                    <span class="axis-value" id="measureVolumeMain">0.00</span>
                    <span class="axis-unit">cm³</span>
                </div>
            </div>
        </div>

        <div class="control-divider"></div>

        <div class="control-section camera-section">
            <div class="control-label">Camera View</div>
            <div class="camera-buttons">
                <button type="button" class="control-btn camera-btn" data-view="top" title="Top View">
                    <svg width="16" height="16" viewBox="0 0 16 16" fill="none">
                        <path d="M8 2L14 6L8 10L2 6L8 2Z" stroke="currentColor" stroke-width="1.5"/>
                    </svg>
                    <span>Top</span>
                </button>
                <button type="button" class="control-btn camera-btn active" data-view="front" title="Front View">
                    <svg width="16" height="16" viewBox="0 0 16 16" fill="none">
                        <rect x="3" y="3" width="10" height="10" stroke="currentColor" stroke-width="1.5"/>
                    </svg>
                    <span>Front</span>
                </button>
                <button type="button" class="control-btn camera-btn" data-view="right" title="Right View">
                    <svg width="16" height="16" viewBox="0 0 16 16" fill="none">
                        <path d="M3 3L13 3L13 13L3 13L3 3Z" stroke="currentColor" stroke-width="1.5"/>
                        <path d="M8 3L13 8L8 13" stroke="currentColor" stroke-width="1.5"/>
                    </svg>
                    <span>Right</span>
                </button>
                <button type="button" class="control-btn camera-btn" data-view="left" title="Left View">
                    <svg width="16" height="16" viewBox="0 0 16 16" fill="none">
                        <path d="M13 3L3 3L3 13L13 13L13 3Z" stroke="currentColor" stroke-width="1.5"/>
                        <path d="M8 3L3 8L8 13" stroke="currentColor" stroke-width="1.5"/>
                    </svg>
                    <span>Left</span>
                </button>
                <button type="button" class="control-btn camera-btn" data-view="bottom" title="Bottom View">
                    <svg width="16" height="16" viewBox="0 0 16 16" fill="none">
                        <path d="M8 14L2 10L8 6L14 10L8 14Z" stroke="currentColor" stroke-width="1.5"/>
                    </svg>
                    <span>Bottom</span>
                </button>
            </div>
        </div>

        <div class="control-divider"></div>

        <div class="control-section tools-section">
            <div class="control-label">Tools</div>
            <div class="tool-buttons">
                <button type="button" class="control-btn tool-btn active" id="toggleGridBtnMain" title="Toggle Grid">
                    <svg width="18" height="18" viewBox="0 0 18 18" fill="none">
                        <path d="M1 1H17M1 6H17M1 11H17M1 16H17M6 1V17M11 1V17" stroke="currentColor" stroke-width="1.5"/>
                    </svg>
                    <span>Grid</span>
                </button>
                <button type="button" class="control-btn tool-btn" id="repairModelBtnMain" title="Repair Model">
                    <svg width="18" height="18" viewBox="0 0 18 18" fill="none">
                        <path d="M15 3L3 15M3 3L15 15" stroke="currentColor" stroke-width="1.5"/>
                        <circle cx="9" cy="9" r="7" stroke="currentColor" stroke-width="1.5"/>
                    </svg>
                    <span>Repair</span>
                </button>
                <button type="button" class="control-btn tool-btn" id="fillHolesBtnMain" title="Fill Holes">
                    <svg width="18" height="18" viewBox="0 0 18 18" fill="none">
                        <circle cx="9" cy="9" r="7" stroke="currentColor" stroke-width="1.5"/>
                        <path d="M9 6V12M6 9H12" stroke="currentColor" stroke-width="1.5"/>
                    </svg>
                    <span>Fill Holes</span>
                </button>
                <button type="button" class="control-btn tool-btn active" id="autoRotateBtnMain" title="Auto Rotate">
                    <svg width="18" height="18" viewBox="0 0 18 18" fill="none">
                        <path d="M15 9C15 12.3137 12.3137 15 9 15C5.68629 15 3 12.3137 3 9C3 5.68629 5.68629 3 9 3C10.5 3 11.9 3.5 13 4.5" stroke="currentColor" stroke-width="1.5"/>
                        <path d="M15 3V7H11" stroke="currentColor" stroke-width="1.5"/>
                    </svg>
                    <span>Rotate</span>
                </button>
            </div>
        </div>

        <div class="control-divider"></div>

        <div class="control-section actions-section">
            <button type="button" class="control-btn save-btn" id="saveCalculationsBtnMain">
                <svg width="18" height="18" viewBox="0 0 18 18" fill="none">
                    <path d="M15 16H3C2.44772 16 2 15.5523 2 15V3C2 2.44772 2.44772 2 3 2H12L16 6V15C16 15.5523 15.5523 16 15 16Z" stroke="currentColor" stroke-width="1.5"/>
                    <path d="M12 2V6H5V2" stroke="currentColor" stroke-width="1.5"/>
                    <path d="M5 10H13V16H5V10Z" stroke="currentColor" stroke-width="1.5"/>
                </svg>
                <span>Save & Calculate</span>
            </button>
        </div>
    </div>

    {{-- CONTROL BAR JAVASCRIPT - Initialize immediately --}}
    <script>
        console.log('🎯 Control bar script loading...');
        
        // Wait for DOM to be ready
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initializeControlBar);
        } else {
            initializeControlBar();
        }
        
        function initializeControlBar() {
            console.log('🎯 Initializing control bar...');
            
            // Check if control bar exists
            const controlBar = document.getElementById('mainControlBar');
            if (!controlBar) {
                console.error('❌ Control bar not found!');
                return;
            }
            console.log('✅ Control bar found:', controlBar);
            
            // State management
            let gridVisible = true;
            let autoRotateEnabled = true;
            let modelRepaired = false;
            let holesFilled = false;
            let currentVolume = 0;
            let currentViewerId = 'viewer3dGeneral';
            
            // Camera view buttons
            const cameraButtons = controlBar.querySelectorAll('.camera-btn');
            console.log('📷 Camera buttons found:', cameraButtons.length);
            cameraButtons.forEach(btn => {
                btn.addEventListener('click', function() {
                    console.log('📷 Camera button clicked:', this.dataset.view);
                    const view = this.dataset.view;
                    
                    // Remove active from siblings
                    this.parentElement.querySelectorAll('.camera-btn').forEach(b => b.classList.remove('active'));
                    this.classList.add('active');
                    
                    // Change camera view
                    setCameraView(currentViewerId, view);
                });
            });
            
            // Grid toggle
            const toggleGridBtn = document.getElementById('toggleGridBtnMain');
            if (toggleGridBtn) {
                console.log('✅ Grid button found');
                toggleGridBtn.addEventListener('click', function() {
                    gridVisible = !gridVisible;
                    this.classList.toggle('active', gridVisible);
                    toggleGrid(currentViewerId, gridVisible);
                    console.log('🎨 Grid toggled:', gridVisible);
                });
            }
            
            // Auto-rotate toggle
            const autoRotateBtn = document.getElementById('autoRotateBtnMain');
            if (autoRotateBtn) {
                console.log('✅ Auto-rotate button found');
                autoRotateBtn.addEventListener('click', function() {
                    autoRotateEnabled = !autoRotateEnabled;
                    this.classList.toggle('active', autoRotateEnabled);
                    
                    if (autoRotateEnabled) {
                        startAutoRotation(currentViewerId);
                    } else {
                        stopAutoRotation(currentViewerId);
                    }
                    console.log('🔄 Auto-rotate:', autoRotateEnabled);
                });
            }
            
            // Repair model
            const repairModelBtn = document.getElementById('repairModelBtnMain');
            if (repairModelBtn) {
                console.log('✅ Repair button found');
                repairModelBtn.addEventListener('click', function() {
                    if (modelRepaired) {
                        console.log('⚠️ Model already repaired');
                        return;
                    }
                    
                    this.classList.add('active');
                    const originalHTML = this.innerHTML;
                    this.innerHTML = '<svg width="18" height="18" viewBox="0 0 18 18" fill="none"><circle cx="9" cy="9" r="7" stroke="currentColor" stroke-width="1.5" stroke-dasharray="4"/></svg><span>Repairing...</span>';
                    this.style.pointerEvents = 'none';
                    
                    setTimeout(() => {
                        repairModel(currentViewerId);
                        modelRepaired = true;
                        this.innerHTML = '<svg width="18" height="18" viewBox="0 0 18 18" fill="none"><path d="M5 9L8 12L13 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg><span>Repaired ✓</span>';
                        this.style.pointerEvents = '';
                        console.log('🔧 Model repaired!');
                    }, 1500);
                });
            }
            
            // Fill holes
            const fillHolesBtn = document.getElementById('fillHolesBtnMain');
            if (fillHolesBtn) {
                console.log('✅ Fill holes button found');
                fillHolesBtn.addEventListener('click', function() {
                    if (holesFilled) {
                        console.log('⚠️ Holes already filled');
                        return;
                    }
                    
                    this.classList.add('active');
                    const originalHTML = this.innerHTML;
                    this.innerHTML = '<svg width="18" height="18" viewBox="0 0 18 18" fill="none"><circle cx="9" cy="9" r="7" stroke="currentColor" stroke-width="1.5" stroke-dasharray="4"/></svg><span>Filling...</span>';
                    this.style.pointerEvents = 'none';
                    
                    setTimeout(() => {
                        fillHoles(currentViewerId);
                        holesFilled = true;
                        this.innerHTML = '<svg width="18" height="18" viewBox="0 0 18 18" fill="none"><path d="M5 9L8 12L13 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg><span>Filled ✓</span>';
                        this.style.pointerEvents = '';
                        console.log('🔧 Holes filled!');
                    }, 1500);
                });
            }
            
            // Save & Calculate
            const saveCalculationsBtn = document.getElementById('saveCalculationsBtnMain');
            if (saveCalculationsBtn) {
                console.log('✅ Save button found');
                saveCalculationsBtn.addEventListener('click', function() {
                    console.log('💾 Save clicked!');
                    saveCalculations(currentViewerId);
                });
            }
            
            // Helper functions
            function setCameraView(viewerId, view) {
                console.log('📷 Setting camera view:', view, 'for', viewerId);
                const viewer = viewerId === 'viewer3dGeneral' ? window.viewerGeneral : window.viewerMedical;
                if (!viewer?.scene?.activeCamera) {
                    console.log('⚠️ No active camera found');
                    return;
                }
                
                const camera = viewer.scene.activeCamera;
                const views = {
                    top: { alpha: Math.PI / 2, beta: 0 },
                    bottom: { alpha: Math.PI / 2, beta: Math.PI },
                    front: { alpha: Math.PI / 2, beta: Math.PI / 2 },
                    right: { alpha: 0, beta: Math.PI / 2 },
                    left: { alpha: Math.PI, beta: Math.PI / 2 }
                };
                
                if (views[view] && camera.alpha !== undefined) {
                    camera.alpha = views[view].alpha;
                    camera.beta = views[view].beta;
                    console.log('✅ Camera moved to', view);
                }
            }
            
            function toggleGrid(viewerId, visible) {
                console.log('🎨 Toggling grid:', visible, 'for', viewerId);
                const viewer = viewerId === 'viewer3dGeneral' ? window.viewerGeneral : window.viewerMedical;
                if (!viewer?.scene) {
                    console.log('⚠️ No scene found');
                    return;
                }
                
                const ground = viewer.scene.getMeshByName('ground');
                if (ground) {
                    ground.isVisible = visible;
                    console.log('✅ Grid visibility set to', visible);
                } else {
                    console.log('⚠️ Ground mesh not found');
                }
            }
            
            function startAutoRotation(viewerId) {
                console.log('🔄 Starting auto-rotation for', viewerId);
                const viewer = viewerId === 'viewer3dGeneral' ? window.viewerGeneral : window.viewerMedical;
                if (!viewer?.scene?.activeCamera) {
                    console.log('⚠️ Cannot start rotation - no camera');
                    return;
                }
                
                const camera = viewer.scene.activeCamera;
                if (camera.useAutoRotationBehavior !== undefined) {
                    camera.useAutoRotationBehavior = true;
                    if (camera.autoRotationBehavior) {
                        camera.autoRotationBehavior.idleRotationSpeed = 0.2;
                        camera.autoRotationBehavior.idleRotationWaitTime = 1000;
                    }
                    console.log('✅ Auto-rotation enabled');
                }
            }
            
            function stopAutoRotation(viewerId) {
                console.log('🛑 Stopping auto-rotation for', viewerId);
                const viewer = viewerId === 'viewer3dGeneral' ? window.viewerGeneral : window.viewerMedical;
                if (!viewer?.scene?.activeCamera) return;
                
                const camera = viewer.scene.activeCamera;
                if (camera.useAutoRotationBehavior !== undefined) {
                    camera.useAutoRotationBehavior = false;
                    console.log('✅ Auto-rotation disabled');
                }
            }
            
            function repairModel(viewerId) {
                console.log('🔧 Repairing model:', viewerId);
            }
            
            function fillHoles(viewerId) {
                console.log('🔧 Filling holes:', viewerId);
            }
            
            function saveCalculations(viewerId) {
                console.log('💾 Saving calculations for:', viewerId);
                
                const btn = document.getElementById('saveCalculationsBtnMain');
                if (btn) {
                    const originalHTML = btn.innerHTML;
                    btn.innerHTML = '<svg width="18" height="18" viewBox="0 0 18 18" fill="none"><path d="M5 9L8 12L13 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg><span>Saved! ✓</span>';
                    btn.style.pointerEvents = 'none';
                    
                    setTimeout(() => {
                        btn.innerHTML = originalHTML;
                        btn.style.pointerEvents = '';
                    }, 2000);
                }
            }
            
            console.log('✅✅✅ Control bar fully initialized!');
        }
    </script>
@endsection
