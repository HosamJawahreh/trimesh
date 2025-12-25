@extends('frontend.layouts.master')

@section('title', '3D Printing Quote - Get Instant Pricing')

@section('meta')
    <meta name="description" content="Upload your 3D files and get instant pricing for your 3D printing projects. Support for STL, OBJ, and PLY files.">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
@endsection

@section('header')
    {{-- No header for full-screen quote experience --}}
@endsection

@section('content')
<style>
    /* ============================================
       VIEWER SELECTION MODAL
       ============================================ */
    .viewer-selection-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100vw;
        height: 100vh;
        background: linear-gradient(180deg, #b8c6db 0%, #f5f7fa 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 999999;
        opacity: 1;
        visibility: visible;
        transition: opacity 0.4s ease, visibility 0.4s ease;
    }

    .viewer-selection-overlay.hidden {
        opacity: 0;
        visibility: hidden;
        pointer-events: none;
    }

    .viewer-selection-container {
        max-width: 1100px;
        width: 90%;
        padding: 30px;
    }

    .viewer-selection-title {
        text-align: center;
        color: #2c3e50;
        margin-bottom: 40px;
    }

    .viewer-selection-title h1 {
        font-size: 36px;
        font-weight: 700;
        margin-bottom: 12px;
        color: #2c3e50;
    }

    .viewer-selection-title p {
        font-size: 16px;
        color: #5a6c7d;
        font-weight: 400;
    }

    .viewer-cards-wrapper {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 30px;
    }

    .viewer-card {
        background: white;
        border-radius: 12px;
        padding: 32px 28px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        cursor: pointer;
        transition: all 0.3s ease;
        position: relative;
        border: 2px solid transparent;
    }

    .viewer-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 30px rgba(0, 0, 0, 0.12);
        border-color: #4CAF50;
    }

    .viewer-card.dental:hover {
        border-color: #2196F3;
    }

    .viewer-card-icon {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 20px;
        font-size: 28px;
        background: #4CAF50;
        color: white;
        box-shadow: 0 4px 12px rgba(76, 175, 80, 0.25);
    }

    .viewer-card.dental .viewer-card-icon {
        background: #2196F3;
        box-shadow: 0 4px 12px rgba(33, 150, 243, 0.25);
    }

    .viewer-card h2 {
        font-size: 24px;
        font-weight: 700;
        margin-bottom: 12px;
        color: #2c3e50;
    }

    .viewer-card-description {
        font-size: 14px;
        color: #6c757d;
        margin-bottom: 24px;
        line-height: 1.6;
    }

    .viewer-card-specs {
        margin-bottom: 24px;
    }

    .viewer-card-specs h3 {
        font-size: 13px;
        font-weight: 700;
        color: #495057;
        margin-bottom: 12px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .spec-item {
        display: flex;
        align-items: flex-start;
        margin-bottom: 10px;
        font-size: 13px;
    }

    .spec-label {
        font-weight: 600;
        color: #495057;
        min-width: 110px;
        flex-shrink: 0;
    }

    .spec-value {
        color: #6c757d;
        line-height: 1.5;
    }

    .viewer-card-button {
        width: 100%;
        padding: 14px;
        background: #4CAF50;
        color: white;
        border: none;
        border-radius: 8px;
        font-size: 15px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s ease;
        box-shadow: 0 2px 8px rgba(76, 175, 80, 0.25);
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .viewer-card.dental .viewer-card-button {
        background: #2196F3;
        box-shadow: 0 2px 8px rgba(33, 150, 243, 0.25);
    }

    .viewer-card-button:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(76, 175, 80, 0.35);
    }

    .viewer-card.dental .viewer-card-button:hover {
        box-shadow: 0 4px 12px rgba(33, 150, 243, 0.35);
    }

    .viewer-card-button:active {
        transform: translateY(0);
    }

    @media (max-width: 968px) {
        .viewer-cards-wrapper {
            grid-template-columns: 1fr;
            gap: 25px;
        }

        .viewer-selection-container {
            padding: 25px 15px;
        }

        .viewer-selection-title h1 {
            font-size: 28px;
        }

        .viewer-selection-title p {
            font-size: 14px;
        }
    }

    /* ============================================
       FULL SCREEN LAYOUT - SHAPEWAYS STYLE
       ============================================ */

    /* Reset everything to full screen */
    html, body, #smooth-wrapper, #smooth-content, main {
        height: 100vh !important;
        width: 100vw !important;
        overflow: hidden !important;
        padding: 0 !important;
        margin: 0 !important;
        position: fixed !important;
        top: 0 !important;
        left: 0 !important;
    }

    /* Main wrapper - Shapeways light blue-gray gradient */
    .quote-page-wrapper {
        position: fixed !important;
        top: 0 !important;
        left: 0 !important;
        width: 100vw !important;
        height: 100vh !important;
        display: flex !important;
        overflow: hidden !important;
        background: linear-gradient(180deg, #b8c6db 0%, #f5f7fa 100%) !important;
    }

    /* Sidebar - Fixed Left */
    .quote-sidebar {
        position: fixed !important;
        left: 0 !important;
        top: 0 !important;
        width: 380px !important;
        height: 100vh !important;
        background: #ffffff !important;
        overflow-y: auto !important;
        overflow-x: hidden !important;
        z-index: 1000 !important;
        box-shadow: 2px 0 12px rgba(0, 0, 0, 0.1) !important;
        visibility: visible;
        opacity: 1;
        transition: opacity 0.3s ease, visibility 0.3s ease;
    }

    /* Hide sidebar when selection modal is visible */
    .viewer-selection-overlay:not(.hidden) ~ .quote-sidebar {
        visibility: hidden;
        opacity: 0;
    }

    /* Viewer - Fixed Right with dynamic background based on mode */
    .quote-viewer {
        position: fixed !important;
        left: 380px !important;
        top: 0 !important;
        right: 0 !important;
        bottom: 0 !important;
        width: calc(100vw - 380px) !important;
        height: 100vh !important;
        background: linear-gradient(180deg, #afc5d8 0%, #e8eef3 100%) !important;
        overflow: hidden !important;
        transition: background 0.3s ease !important;
        visibility: visible;
        opacity: 1;
    }

    /* Hide viewer when selection modal is visible */
    .viewer-selection-overlay:not(.hidden) ~ .quote-viewer {
        visibility: hidden;
        opacity: 0;
    }

    /* General mode - Blue gradient */
    .quote-viewer.mode-general {
        background: linear-gradient(180deg, #4a90e2 0%, #7ab8f5 50%, #b8d8f7 100%) !important;
    }

    /* Medical mode - Original Shapeways gradient */
    .quote-viewer.mode-medical {
        background: linear-gradient(180deg, #afc5d8 0%, #e8eef3 100%) !important;
    }

    /* Dental mode - Same as Medical */
    .quote-viewer.mode-dental {
        background: linear-gradient(180deg, #afc5d8 0%, #e8eef3 100%) !important;
    }

    /* 3D Viewer Canvas Container */
    #viewer3dGeneral,
    #viewer3dMedical {
        position: absolute !important;
        top: 0 !important;
        left: 0 !important;
        width: 100% !important;
        height: 100% !important;
        background: transparent !important;
    }

    #viewer3dGeneral canvas,
    #viewer3dMedical canvas {
        position: absolute !important;
        top: 0 !important;
        left: 0 !important;
        width: 100% !important;
        height: 100% !important;
        background: transparent !important;
    }

    /* Hide old control bars from quote-viewer.blade.php */
    #controlBarGeneral,
    #controlBarMedical {
        display: none !important;
    }

    /* Override quote-viewer structure */
    .dgm-3d-quote-area {
        padding: 0 !important;
        margin: 0 !important;
        height: 100vh !important;
        width: 100vw !important;
        position: fixed !important;
        top: 0 !important;
        left: 0 !important;
    }

    .dgm-3d-quote-area .container {
        max-width: none !important;
        width: 100vw !important;
        height: 100vh !important;
        padding: 0 !important;
        margin: 0 !important;
    }

    .quote-form-container-3d {
        height: 100vh !important;
        width: 100vw !important;
        margin: 0 !important;
        padding: 0 !important;
    }

    .quote-form-container-3d .row {
        height: 100vh !important;
        width: 100vw !important;
        margin: 0 !important;
        padding: 0 !important;
    }

    .quote-form-container-3d .col-12,
    .quote-form-container-3d .col-lg-11,
    .quote-form-container-3d .card {
        height: 100vh !important;
        width: 100vw !important;
        margin: 0 !important;
        padding: 0 !important;
        border: none !important;
        border-radius: 0 !important;
        background: transparent !important;
    }

    /* Sidebar column - Fixed left */
    .quote-form-container-3d .col-lg-3 {
        position: fixed !important;
        left: 0 !important;
        top: 0 !important;
        width: 380px !important;
        height: 100vh !important;
        z-index: 1000 !important;
        background: #ffffff !important;
        overflow-y: auto !important;
        padding: 20px !important;
        margin: 0 !important;
    }

    /* Viewer column - Fixed right */
    .quote-form-container-3d .col-lg-9 {
        position: fixed !important;
        left: 380px !important;
        top: 0 !important;
        right: 0 !important;
        bottom: 0 !important;
        width: calc(100vw - 380px) !important;
        height: 100vh !important;
        background: transparent !important;
        padding: 0 !important;
        margin: 0 !important;
    }

    /* Control Bar - Shapeways style with proper spacing */
    /* Bottom Bar - Simplified Toolbar Style */
    .viewer-bottom-controls {
        position: fixed !important;
        bottom: 20px !important;
        left: 400px !important;
        right: 20px !important;
        width: calc(100vw - 420px) !important;
        height: auto !important;
        background: rgba(255, 255, 255, 0.95) !important;
        backdrop-filter: blur(10px) !important;
        border-radius: 12px !important;
        display: flex !important;
        align-items: center !important;
        justify-content: space-between !important;
        padding: 8px 12px !important;
        gap: 12px !important;
        z-index: 999999 !important;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15) !important;
        pointer-events: auto !important;
        visibility: visible;
        opacity: 1;
        transition: opacity 0.3s ease, visibility 0.3s ease;
    }

    /* Hide bottom controls when selection modal is visible */
    .viewer-selection-overlay:not(.hidden) ~ * .viewer-bottom-controls,
    .viewer-selection-overlay:not(.hidden) ~ .viewer-bottom-controls {
        visibility: hidden;
        opacity: 0;
        pointer-events: none;
    }

    /* Bottom Bar Groups */
    .bottom-bar-group {
        display: flex !important;
        align-items: center !important;
        gap: 8px !important;
    }

    /* Bottom Bar Divider */
    .bottom-bar-divider {
        width: 1px;
        height: 32px;
        background: rgba(0,0,0,0.1);
        margin: 0 4px;
    }

    /* Upload Button - Simplified */
    .upload-btn-simple {
        display: flex !important;
        align-items: center !important;
        gap: 6px !important;
        padding: 8px 14px !important;
        background: linear-gradient(135deg, #0047AD 0%, #003580 100%) !important;
        color: white !important;
        border: none !important;
        border-radius: 8px !important;
        font-size: 13px !important;
        font-weight: 500 !important;
        cursor: pointer !important;
        transition: all 0.2s ease !important;
        position: relative !important;
    }

    .upload-btn-simple:hover {
        transform: translateY(-1px) !important;
        box-shadow: 0 4px 12px rgba(0, 71, 173, 0.3) !important;
    }

    .upload-btn-simple svg {
        width: 16px;
        height: 16px;
    }

    /* File Count Badge - Small behind button */
    .upload-file-badge {
        position: absolute !important;
        top: -6px !important;
        right: -6px !important;
        background: #10b981 !important;
        color: white !important;
        border-radius: 10px !important;
        padding: 2px 6px !important;
        font-size: 10px !important;
        font-weight: 700 !important;
        line-height: 1 !important;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2) !important;
        display: none !important;
        min-width: 18px !important;
        text-align: center !important;
    }

    .upload-file-badge.show {
        display: block !important;
    }

    /* Price Display in Bottom Bar */
    .bottom-price-display {
        display: none !important;
        align-items: center !important;
        gap: 16px !important;
        padding: 6px 16px !important;
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%) !important;
        border-radius: 8px !important;
        border: 1px solid #dee2e6 !important;
    }

    .bottom-price-display.show {
        display: flex !important;
    }

    .info-item {
        display: flex;
        flex-direction: column;
        align-items: flex-end;
    }

    .info-label {
        font-size: 10px !important;
        font-weight: 600 !important;
        color: #6c757d !important;
        text-transform: uppercase !important;
        letter-spacing: 0.5px !important;
        margin-bottom: 2px;
    }

    .info-value {
        font-size: 15px !important;
        font-weight: 700 !important;
        color: #2c3e50 !important;
    }

    .info-value.price {
        font-size: 18px !important;
        color: #0047AD !important;
    }

    .info-divider {
        width: 1px;
        height: 30px;
        background: rgba(0,0,0,0.1);
    }

    /* Request Quote Button in Bottom Bar */
    .bottom-quote-btn {
        padding: 8px 20px !important;
        background: linear-gradient(135deg, #4a90e2 0%, #357abd 100%) !important;
        color: white !important;
        border: none !important;
        border-radius: 8px !important;
        font-size: 13px !important;
        font-weight: 600 !important;
        cursor: pointer !important;
        transition: all 0.2s ease !important;
        white-space: nowrap;
    }

    .bottom-quote-btn:hover {
        transform: translateY(-1px) !important;
        box-shadow: 0 4px 12px rgba(74, 144, 226, 0.4) !important;
    }

    .price-label {
        font-size: 11px !important;
        font-weight: 600 !important;
        color: #6c757d !important;
        text-transform: uppercase !important;
        letter-spacing: 0.5px !important;
    }

    .price-value {
        font-size: 18px !important;
        font-weight: 700 !important;
        color: #0047AD !important;
    }

    /* Control Sections */
    .control-section {
        display: flex !important;
        flex-direction: column !important;
        gap: 8px !important;
        flex-shrink: 0 !important;
    }

    /* Actions Section - Keep buttons horizontal */
    .control-section.actions-section {
        flex-direction: row !important;
        align-items: center !important;
        gap: 12px !important;
    }

    .control-label {
        font-size: 0.65rem !important;
        font-weight: 700 !important;
        color: #6c757d !important;
        text-transform: uppercase !important;
        letter-spacing: 1px !important;
        margin: 0 !important;
        padding: 0 !important;
    }

    /* Measurement Items */
    .measurement-items {
        display: flex !important;
        gap: 10px !important;
        flex-wrap: nowrap !important;
    }

    .measurement-item {
        display: flex !important;
        align-items: baseline !important;
        gap: 4px !important;
        padding: 6px 10px !important;
        background: #f8f9fa !important;
        border-radius: 6px !important;
        white-space: nowrap !important;
        font-size: 0.75rem !important;
    }

    .measurement-item.volume {
        background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%) !important;
    }

    .axis-label {
        font-weight: 700 !important;
        color: #495057 !important;
    }

    .axis-value {
        font-family: 'Courier New', monospace !important;
        font-weight: 600 !important;
        color: #212529 !important;
    }

    .axis-unit {
        font-size: 0.7rem !important;
        color: #6c757d !important;
    }

    /* Camera & Tool Buttons */
    .camera-grid,
    .tools-grid {
        display: flex !important;
        gap: 8px !important;
        flex-wrap: nowrap !important;
    }

    /* Camera section - compact view with dropdown on hover */
    .camera-section {
        position: relative !important;
    }

    .camera-section .control-label {
        cursor: pointer !important;
        padding: 8px 12px !important;
        background: #ffffff !important;
        border: 1.5px solid #dee2e6 !important;
        border-radius: 8px !important;
        transition: all 0.2s ease !important;
    }

    .camera-section .control-label:hover {
        background: #f8f9fa !important;
        border-color: #007bff !important;
        color: #007bff !important;
    }

    .camera-section .camera-grid {
        position: absolute !important;
        bottom: 100% !important;
        left: 0 !important;
        margin-bottom: 8px !important;
        background: white !important;
        border: 1px solid #dee2e6 !important;
        border-radius: 8px !important;
        padding: 8px !important;
        box-shadow: 0 4px 12px rgba(0,0,0,0.1) !important;
        opacity: 0 !important;
        visibility: hidden !important;
        transform: translateY(10px) !important;
        transition: all 0.2s ease !important;
        z-index: 1000 !important;
        display: grid !important;
        grid-template-columns: repeat(3, 1fr) !important;
        gap: 6px !important;
    }

    .camera-section:hover .camera-grid {
        opacity: 1 !important;
        visibility: visible !important;
        transform: translateY(0) !important;
    }

    .control-btn {
        display: flex !important;
        flex-direction: column !important;
        align-items: center !important;
        justify-content: center !important;
        gap: 4px !important;
        padding: 8px 12px !important;
        background: #ffffff !important;
        border: 1.5px solid #dee2e6 !important;
        border-radius: 8px !important;
        cursor: pointer !important;
        transition: all 0.2s ease !important;
        color: #495057 !important;
        font-size: 0.7rem !important;
        font-weight: 600 !important;
        min-width: 55px !important;
        pointer-events: auto !important;
        user-select: none !important;
    }

    .control-btn:hover {
        background: #f8f9fa !important;
        border-color: #5d8fcc !important;
        color: #5d8fcc !important;
        transform: translateY(-2px) !important;
        box-shadow: 0 4px 12px rgba(93, 143, 204, 0.2) !important;
    }

    .control-btn.active {
        background: linear-gradient(135deg, #5d8fcc 0%, #4a7bb8 100%) !important;
        border-color: #5d8fcc !important;
        color: #fff !important;
        box-shadow: 0 4px 12px rgba(93, 143, 204, 0.4) !important;
    }

    .control-btn.active svg {
        stroke: #fff !important;
    }

    .control-btn svg {
        pointer-events: none !important;
    }

    /* Upload Button */
    .upload-btn {
        flex-direction: row !important;
        gap: 10px !important;
        padding: 12px 24px !important;
        background: linear-gradient(135deg, #0047AD 0%, #003580 100%) !important;
        border: none !important;
        color: #fff !important;
        font-size: 0.9rem !important;
        font-weight: 700 !important;
        box-shadow: 0 4px 16px rgba(0, 71, 173, 0.3) !important;
        min-width: auto !important;
        transition: all 0.3s ease !important;
    }

    .upload-btn:hover {
        background: linear-gradient(135deg, #003580 0%, #002461 100%) !important;
        transform: translateY(-3px) !important;
        box-shadow: 0 6px 24px rgba(0, 71, 173, 0.5) !important;
    }

    .upload-btn svg {
        stroke: #fff !important;
    }

    .upload-section {
        display: flex !important;
        align-items: center !important;
        gap: 12px !important;
    }

    .upload-count {
        display: flex !important;
        flex-direction: column !important;
        align-items: center !important;
        gap: 2px !important;
    }

    .count-badge {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%) !important;
        color: white !important;
        font-size: 16px !important;
        font-weight: 700 !important;
        padding: 4px 12px !important;
        border-radius: 20px !important;
        box-shadow: 0 2px 8px rgba(16, 185, 129, 0.3) !important;
        animation: pulse 2s infinite !important;
    }

    @keyframes pulse {
        0%, 100% { transform: scale(1); }
        50% { transform: scale(1.05); }
    }

    .count-label {
        font-size: 11px !important;
        font-weight: 600 !important;
        color: #666 !important;
        text-transform: uppercase !important;
        letter-spacing: 0.5px !important;
    }

    /* Share Button */
    .share-btn {
        flex-direction: row !important;
        gap: 8px !important;
        padding: 10px 20px !important;
        background: linear-gradient(135deg, #3498db 0%, #2980b9 100%) !important;
        border: none !important;
        color: #fff !important;
        font-size: 0.85rem !important;
        font-weight: 700 !important;
        box-shadow: 0 4px 16px rgba(52, 152, 219, 0.3) !important;
        min-width: auto !important;
    }

    .share-btn:hover {
        background: linear-gradient(135deg, #2980b9 0%, #1f5f8b 100%) !important;
        transform: translateY(-2px) !important;
        box-shadow: 0 6px 20px rgba(52, 152, 219, 0.4) !important;
    }

    /* Save Button */
    .save-btn {
        flex-direction: row !important;
        gap: 8px !important;
        padding: 10px 20px !important;
        background: linear-gradient(135deg, #10b981 0%, #059669 100%) !important;
        border: none !important;
        color: #fff !important;
        font-size: 0.85rem !important;
        font-weight: 700 !important;
        box-shadow: 0 4px 16px rgba(16, 185, 129, 0.3) !important;
        min-width: auto !important;
    }

    .save-btn:hover {
        background: linear-gradient(135deg, #059669 0%, #047857 100%) !important;
        transform: translateY(-2px) !important;
        box-shadow: 0 6px 20px rgba(16, 185, 129, 0.4) !important;
    }

    /* Divider */
    .control-divider {
        width: 2px !important;
        height: 45px !important;
        background: linear-gradient(to bottom, transparent, #dee2e6 20%, #dee2e6 80%, transparent) !important;
        flex-shrink: 0 !important;
    }

    /* Form visibility */
    #generalForm3d {
        display: block !important;
    }

    #medicalForm3d {
        display: none !important;
    }

    /* Measurement Panel */
    #measurementPanel {
        position: fixed !important;
        top: 80px !important;
        left: 410px !important;
        background: rgba(255, 255, 255, 0.98) !important;
        backdrop-filter: blur(10px) !important;
        border-radius: 12px !important;
        padding: 20px !important;
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.2) !important;
        z-index: 9999 !important;
        min-width: 300px !important;
        max-width: 350px !important;
        display: none !important;
        border: 2px solid #007bff !important;
    }

    #measurementPanel.active {
        display: block !important;
    }

    #measurementPanel .panel-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 15px;
        padding-bottom: 12px;
        border-bottom: 2px solid #e0e0e0;
    }

    #measurementPanel .panel-title {
        font-size: 16px;
        font-weight: 600;
        color: #333;
    }

    #measurementPanel .close-btn {
        background: none;
        border: none;
        font-size: 24px;
        color: #999;
        cursor: pointer;
        padding: 0;
        width: 30px;
        height: 30px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        transition: all 0.2s;
    }

    #measurementPanel .close-btn:hover {
        background: #f0f0f0;
        color: #333;
    }

    #measurementPanel .measurement-info {
        font-size: 13px;
        color: #666;
        margin-bottom: 15px;
        line-height: 1.5;
    }

    #measurementPanel .measurement-result {
        background: #f8f9fa;
        border-radius: 8px;
        padding: 15px;
        margin-top: 15px;
    }

    #measurementPanel .measurement-value {
        font-size: 28px;
        font-weight: 700;
        color: #ff6b35;
        margin-bottom: 5px;
    }

    #measurementPanel .measurement-label {
        font-size: 12px;
        color: #666;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    #measurementPanel .measurement-points {
        margin-top: 12px;
        padding-top: 12px;
        border-top: 1px solid #e0e0e0;
        font-size: 12px;
        color: #666;
    }

    #measurementPanel .clear-measurements-btn {
        width: 100%;
        padding: 10px;
        margin-top: 15px;
        background: #f44336;
        color: white;
        border: none;
        border-radius: 6px;
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
        transition: background 0.2s;
    }

    #measurementPanel .clear-measurements-btn:hover {
        background: #d32f2f;
    }

    /* Unit buttons */
    .unit-btn {
        flex: 1;
        padding: 8px 12px;
        background: #f5f5f5;
        border: 2px solid #e0e0e0;
        border-radius: 6px;
        font-size: 13px;
        font-weight: 600;
        color: #666;
        cursor: pointer;
        transition: all 0.2s;
    }

    .unit-btn:hover {
        background: #e8e8e8;
        border-color: #ccc;
    }

    .unit-btn.active {
        background: #2196f3;
        border-color: #2196f3;
        color: white;
    }

    /* Measurement Panel - Shapeways Style */
</style>

<div class="quote-page-wrapper">
    <!-- Measurement Panel (Shapeways Style) -->
    <div id="measurementPanel">
        <div class="panel-header">
            <div class="panel-title">📏 Thickness Measurement</div>
            <button class="close-btn" id="closeMeasurementPanel">&times;</button>
        </div>
        <div class="measurement-info" style="color: #ff9800; font-size: 13px; margin-bottom: 15px;">
            Hover over model surfaces
        </div>

        <!-- Measurement Units -->
        <div style="margin-bottom: 15px;">
            <div style="font-weight: 600; margin-bottom: 8px; font-size: 13px; color: #666;">Measurement Units</div>
            <div style="display: flex; gap: 10px;">
                <button class="unit-btn active" data-unit="mm">mm</button>
                <button class="unit-btn" data-unit="m">m</button>
                <button class="unit-btn" data-unit="in">in</button>
            </div>
        </div>

        <!-- Current Reading -->
        <div style="margin-bottom: 20px;">
            <div style="font-weight: 600; margin-bottom: 8px; font-size: 13px; color: #666;">Current Reading</div>
            <div id="currentReading" style="background: #fffbea; padding: 15px; border-radius: 8px; border: 2px solid #ffd54f;">
                <div id="thicknessValue" style="font-size: 20px; font-weight: 700; color: #333; margin-bottom: 4px;">-</div>
                <div style="font-size: 12px; color: #999;">Hover over model<br>to start measuring</div>
            </div>
        </div>

        <!-- Model Dimensions -->
        <div style="margin-bottom: 15px;">
            <div style="font-weight: 600; margin-bottom: 8px; font-size: 13px; color: #666;">Model Dimensions</div>
            <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 10px; font-size: 12px;">
                <div>
                    <div style="color: #999; margin-bottom: 4px;">Width</div>
                    <div style="font-weight: 600;" id="modelWidth">0.00 mm</div>
                </div>
                <div>
                    <div style="color: #999; margin-bottom: 4px;">Height</div>
                    <div style="font-weight: 600;" id="modelHeight">0.00 mm</div>
                </div>
                <div>
                    <div style="color: #999; margin-bottom: 4px;">Depth</div>
                    <div style="font-weight: 600;" id="modelDepth">0.00 mm</div>
                </div>
            </div>
        </div>

        <!-- Visual Indicators -->
        <div>
            <div style="font-weight: 600; margin-bottom: 8px; font-size: 13px; color: #666;">Visual Indicators</div>
            <div style="font-size: 12px;">
                <div style="margin-bottom: 6px; display: flex; align-items: center; gap: 8px;">
                    <span style="width: 12px; height: 12px; background: #ff9800; border-radius: 50%; display: inline-block;"></span>
                    <span>Entry point (orange)</span>
                </div>
                <div style="margin-bottom: 6px; display: flex; align-items: center; gap: 8px;">
                    <span style="width: 12px; height: 12px; background: #4caf50; border-radius: 50%; display: inline-block;"></span>
                    <span>Exit point (green)</span>
                </div>
                <div style="display: flex; align-items: center; gap: 8px;">
                    <span style="width: 20px; height: 2px; background: #2196f3; display: inline-block;"></span>
                    <span>Measurement line</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Viewer Selection Modal -->
    <div class="viewer-selection-overlay" id="viewerSelectionModal">
        <div class="viewer-selection-container">
            <div class="viewer-selection-title">
                <h1>Choose Your 3D Printing Experience</h1>
            </div>

            <div class="viewer-cards-wrapper">
                <!-- General Viewer Card -->
                <div class="viewer-card general" data-viewer-type="general">
                    <div class="viewer-card-icon">
                        <i class="fas fa-cube"></i>
                    </div>
                    <h2>General Viewer</h2>
                    <p class="viewer-card-description">
                        Perfect for prototypes, functional parts, and standard 3D printing projects with a wide range of materials and technologies.
                    </p>

                    <div class="viewer-card-specs">
                        <h3>Specifications</h3>
                        <div class="spec-item">
                            <span class="spec-label">Technology:</span>
                            <span class="spec-value">FDM, SLA, SLS</span>
                        </div>
                        <div class="spec-item">
                            <span class="spec-label">Materials:</span>
                            <span class="spec-value">PLA, ABS, PETG, Nylon, Resin</span>
                        </div>
                        <div class="spec-item">
                            <span class="spec-label">Colors:</span>
                            <span class="spec-value">Full cosmetic color range</span>
                        </div>
                        <div class="spec-item">
                            <span class="spec-label">Layer Height:</span>
                            <span class="spec-value">Variable based on technology</span>
                        </div>
                        <div class="spec-item">
                            <span class="spec-label">Best For:</span>
                            <span class="spec-value">Prototypes, parts, figurines</span>
                        </div>
                    </div>

                    <button class="viewer-card-button" onclick="selectViewer('general')">
                        Launch General Viewer
                    </button>
                </div>

                <!-- Dental Viewer Card -->
                <div class="viewer-card dental" data-viewer-type="dental">
                    <div class="viewer-card-icon">
                        <i class="fas fa-tooth"></i>
                    </div>
                    <h2>Dental Viewer</h2>
                    <p class="viewer-card-description">
                        Specialized for dental applications with certified biocompatible materials and medical-grade quality standards.
                    </p>

                    <div class="viewer-card-specs">
                        <h3>Specifications</h3>
                        <div class="spec-item">
                            <span class="spec-label">Technology:</span>
                            <span class="spec-value">SLA / DLP only</span>
                        </div>
                        <div class="spec-item">
                            <span class="spec-label">Materials:</span>
                            <span class="spec-value">Biocompatible certified resins</span>
                        </div>
                        <div class="spec-item">
                            <span class="spec-label">Colors:</span>
                            <span class="spec-value">Limited, certified range</span>
                        </div>
                        <div class="spec-item">
                            <span class="spec-label">Layer Height:</span>
                            <span class="spec-value">Fixed, validated (25-50μm)</span>
                        </div>
                        <div class="spec-item">
                            <span class="spec-label">Best For:</span>
                            <span class="spec-value">Dental models, surgical guides, aligners</span>
                        </div>
                    </div>

                    <button class="viewer-card-button" onclick="selectViewer('dental')">
                        Launch Dental Viewer
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Sidebar -->
    <div class="quote-sidebar">
        @include('frontend.pages.quote-viewer')
    </div>



    <!-- Control Bar - Simplified Toolbar Design -->
    <div class="viewer-bottom-controls" id="mainControlBar" style="display: none !important;">

        <!-- Left Group: Upload Button -->
        <div class="bottom-bar-group">
            <input type="file" id="fileInputBottomBar" style="display: none;" accept=".stl,.obj,.ply" multiple>
            <button type="button" class="upload-btn-simple" onclick="document.getElementById('fileInputBottomBar').click()" title="Upload 3D Files">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                    <polyline points="17 8 12 3 7 8"></polyline>
                    <line x1="12" y1="3" x2="12" y2="15"></line>
                </svg>
                <span>Upload Files</span>
                <span class="upload-file-badge" id="uploadFileBadge">0</span>
            </button>
        </div>

        <!-- Center: Spacer -->
        <div style="flex: 1;"></div>

        <!-- Right Group: Volume, Price & Quote Button -->
        <div class="bottom-price-display" id="bottomPriceDisplay">
            <!-- Volume -->
            <div class="info-item">
                <span class="info-label">Volume</span>
                <span class="info-value" id="bottomTotalVolume">0 cm³</span>
            </div>

            <div class="info-divider"></div>

            <!-- Price -->
            <div class="info-item">
                <span class="info-label">Total Price</span>
                <span class="info-value price" id="bottomTotalPrice">$0.00</span>
            </div>

            <div class="info-divider"></div>

            <!-- Request Quote Button -->
            <button type="button" class="bottom-quote-btn" id="bottomRequestQuoteBtn" onclick="handleRequestQuote()">
                Request Quote →
            </button>
        </div>

    </div>
</div>

<script>
    // ============================================
    // CONTROL BAR FUNCTIONALITY - THREE.JS
    // VERSION: CANVAS-DIRECT-HANDLERS-{{ time() }}
    // ============================================
    console.log('🔥 QUOTE SCRIPT - CANVAS DIRECT HANDLERS - TS:', {{ time() }});

    // Global state for measurement tool (needs to be accessible by event handlers)
    let measurementMode = false;
    let measurementPoints = [];
    let measurementMarkers = [];
    let measurementLine = null;

    // Global state for pan tool
    let panMode = false;
    let isPanning = false;
    let selectedModel = null; // The model being dragged
    let dragPlane = null; // Invisible plane for dragging
    let dragOffset = null; // Will be initialized as THREE.Vector3 when needed
    let originalMaterialEmissive = null; // Store original emissive color for highlight

    // Flag to prevent duplicate event listeners
    let measurementClickHandlerSet = false;

    // ============================================
    // VIEWER TYPE SELECTION
    // ============================================
    let selectedViewerType = null; // 'general' or 'dental'

    function selectViewer(type) {
        console.log(`🎯 Viewer selected: ${type.toUpperCase()}`);
        selectedViewerType = type;

        // Store selection in sessionStorage
        sessionStorage.setItem('viewerType', type);

        // Update URL with viewer type parameter
        const url = new URL(window.location.href);
        url.searchParams.set('viewer', type);
        window.history.pushState({}, '', url);

        // Hide the selection modal with animation
        const modal = document.getElementById('viewerSelectionModal');
        if (modal) {
            modal.classList.add('hidden');
            console.log('✅ Selection modal hidden');
        }

        // Update viewer container class for styling
        const viewerContainer = document.querySelector('.quote-viewer');
        if (viewerContainer) {
            viewerContainer.classList.remove('mode-general', 'mode-dental');
            viewerContainer.classList.add(`mode-${type}`);
        }

        // Update sidebar to show correct form (with retries to ensure forms are loaded)
        let retryCount = 0;
        const maxRetries = 10;

        function tryUpdateSidebar() {
            const generalForm = document.getElementById('generalForm3d');
            const medicalForm = document.getElementById('medicalForm3d');

            if (generalForm && medicalForm) {
                updateSidebarForViewerType(type);
                console.log('✅ Forms found and updated');
            } else if (retryCount < maxRetries) {
                retryCount++;
                console.log(`⏳ Waiting for forms to load... (attempt ${retryCount}/${maxRetries})`);
                setTimeout(tryUpdateSidebar, 100);
            } else {
                console.error('❌ Forms not found after 10 retries');
            }
        }

        // Start trying to update sidebar
        setTimeout(tryUpdateSidebar, 100);

        // Initialize the viewer after a short delay
        setTimeout(() => {
            initializeViewer(type);
        }, 400);
    }

    function updateSidebarForViewerType(type) {
        // Show/hide appropriate forms in sidebar using correct IDs
        const generalForm = document.getElementById('generalForm3d');
        const medicalForm = document.getElementById('medicalForm3d');

        console.log('🔄 Updating sidebar forms:', {
            type: type,
            generalFormFound: !!generalForm,
            medicalFormFound: !!medicalForm
        });

        if (type === 'general') {
            if (generalForm) {
                // Use cssText to ensure it overrides everything
                generalForm.setAttribute('style', 'display: block !important;');
                console.log('✅ General form displayed');
            }
            if (medicalForm) {
                medicalForm.setAttribute('style', 'display: none !important;');
                console.log('✅ Medical form hidden');
            }
        } else if (type === 'dental') {
            if (generalForm) {
                generalForm.setAttribute('style', 'display: none !important;');
                console.log('✅ General form hidden');
            }
            if (medicalForm) {
                medicalForm.setAttribute('style', 'display: block !important;');
                console.log('✅ Dental/Medical form displayed');
            }
        }

        // Verify the changes were applied
        setTimeout(() => {
            console.log('📊 Final verification:', {
                generalDisplay: generalForm ? window.getComputedStyle(generalForm).display : 'not found',
                medicalDisplay: medicalForm ? window.getComputedStyle(medicalForm).display : 'not found'
            });
        }, 100);
    }

    function initializeViewer(type) {
        console.log(`🚀 Initializing viewer for ${type} mode...`);

        // Both General and Dental use the SAME viewer (General viewer)
        // The ONLY difference is the sidebar form options
        // This ensures identical functionality, tools, and file handling

        if (typeof initGeneralViewer === 'function') {
            initGeneralViewer();
            console.log('✅ General viewer initialized (used for both General and Dental modes)');
        } else if (window.viewerGeneral) {
            console.log('✅ General viewer already initialized');
        }

        // Set the global viewer reference for both modes
        window.viewer = window.viewerGeneral;

        console.log(`📋 Viewer mode: ${type} (using General viewer with ${type} form)`);
    }

    // Check if viewer type was already selected (page refresh or URL parameter)
    function checkExistingViewerSelection() {
        // First check URL parameter
        const urlParams = new URLSearchParams(window.location.search);
        const urlViewerType = urlParams.get('viewer');

        // If URL has a viewer parameter, use it (highest priority)
        if (urlViewerType && (urlViewerType === 'general' || urlViewerType === 'dental')) {
            console.log(`🔄 Loading viewer from URL parameter: ${urlViewerType}`);
            selectViewer(urlViewerType);
            return;
        }

        // If no URL parameter, ALWAYS show the selection modal
        // (Don't auto-restore from sessionStorage on direct /quote visits)
        console.log('📋 No viewer parameter in URL - showing selection modal');
        const modal = document.getElementById('viewerSelectionModal');
        if (modal) {
            modal.classList.remove('hidden');
        }

        // Clear any old sessionStorage to prevent confusion
        sessionStorage.removeItem('viewerType');
    }

    // Make selectViewer globally accessible
    window.selectViewer = selectViewer;

    // Wait for everything to load including quote-viewer scripts
    window.addEventListener('load', function() {
        console.log('🎯 Initializing control bar for Three.js...');
        console.log('🔍 Script loaded successfully at:', new Date().toLocaleTimeString());

        // Check for existing viewer selection first
        checkExistingViewerSelection();

        // Small delay to ensure quote-viewer is fully initialized
        setTimeout(initControls, 500);
        setTimeout(initFileUpload, 600);
    });

    // ============================================
    // FILE UPLOAD FROM BOTTOM BAR
    // ============================================
    function initFileUpload() {
        const fileInputBottomBar = document.getElementById('fileInputBottomBar');
        const fileBadge = document.getElementById('uploadFileBadge');

        if (!fileInputBottomBar) {
            console.error('❌ Bottom bar file input not found!');
            return;
        }

        console.log('📤 File upload initialized from bottom bar');

        // Handle file selection
        fileInputBottomBar.addEventListener('change', function(e) {
            const files = e.target.files;
            if (files && files.length > 0) {
                console.log(`📁 Selected ${files.length} file(s) from bottom bar`);

                // Detect which form is active (General or Dental)
                const generalForm = document.getElementById('generalForm3d');
                const medicalForm = document.getElementById('medicalForm3d');

                let mainFileInput = null;
                let activeForm = 'unknown';

                // Check which form is currently visible
                if (generalForm && window.getComputedStyle(generalForm).display !== 'none') {
                    mainFileInput = document.getElementById('fileInput3d');
                    activeForm = 'General';
                } else if (medicalForm && window.getComputedStyle(medicalForm).display !== 'none') {
                    mainFileInput = document.getElementById('fileInput3dMedical');
                    activeForm = 'Dental';
                } else {
                    // Fallback: try both inputs
                    mainFileInput = document.getElementById('fileInput3d') || document.getElementById('fileInput3dMedical');
                    activeForm = 'Fallback';
                }

                console.log(`🎯 Active form detected: ${activeForm}, delegating to: ${mainFileInput ? mainFileInput.id : 'none'}`);

                if (mainFileInput) {
                    // Transfer files to main input
                    const dataTransfer = new DataTransfer();
                    for (let i = 0; i < files.length; i++) {
                        dataTransfer.items.add(files[i]);
                    }
                    mainFileInput.files = dataTransfer.files;

                    // Trigger change event
                    mainFileInput.dispatchEvent(new Event('change', { bubbles: true }));

                    // Update file count badge
                    updateFileBadge();

                    // Show success notification
                    if (window.showToolbarNotification) {
                        showToolbarNotification(
                            `✅ ${files.length} file${files.length > 1 ? 's' : ''} uploaded to ${activeForm} viewer`,
                            'success',
                            3000
                        );
                    }
                } else {
                    console.error('❌ Main file input not found!');
                }
            }

            // Reset input
            e.target.value = '';
        });

        // Update file count badge
        function updateFileBadge() {
            // Get file count from viewer
            let fileCount = 0;

            if (window.viewer && window.viewer.uploadedFiles) {
                fileCount = window.viewer.uploadedFiles.length;
            } else if (window.viewerGeneral && window.viewerGeneral.uploadedFiles) {
                fileCount = window.viewerGeneral.uploadedFiles.length;
            }

            if (fileBadge) {
                if (fileCount > 0) {
                    fileBadge.textContent = fileCount;
                    fileBadge.classList.add('show');
                } else {
                    fileBadge.classList.remove('show');
                }
            }
        }

        // Listen for file uploads from other sources to update badge
        window.addEventListener('filesUploaded', function(e) {
            updateFileBadge();
        });

        // Listen for file removals to update badge
        document.addEventListener('fileRemoved', function() {
            updateFileBadge();
        });

        // Make updateFileBadge globally accessible
        window.updateFileBadge = updateFileBadge;

        console.log('✅ Bottom bar file upload ready');
    }

    // PRICE SYNC TO BOTTOM BAR
    window.syncPriceToBottomBar = function() {
        const sidebarPrice = document.getElementById('quoteTotalPriceGeneral');
        const sidebarVolume = document.getElementById('quoteTotalVolumeGeneral');
        const bottomPrice = document.getElementById('bottomTotalPrice');
        const bottomVolume = document.getElementById('bottomTotalVolume');
        const bottomDisplay = document.getElementById('bottomPriceDisplay');

        if (sidebarPrice && bottomPrice && bottomDisplay) {
            const priceText = sidebarPrice.textContent || '$0.00';
            bottomPrice.textContent = priceText;

            // Sync volume if available
            if (sidebarVolume && bottomVolume) {
                const volumeText = sidebarVolume.textContent || '0 cm³';
                bottomVolume.textContent = volumeText;
            }

            // Show bottom price if sidebar price is visible and has value
            if (sidebarPrice.style.display !== 'none' && priceText !== '$0' && priceText !== '$0.00' && priceText.trim() !== '') {
                bottomDisplay.classList.add('show');
                console.log('💰 Price and volume synced to bottom bar:', priceText, bottomVolume?.textContent);
            } else {
                bottomDisplay.classList.remove('show');
            }
        }
    };

    // Handle Request Quote button click
    window.handleRequestQuote = function() {
        const sidebarBtn = document.getElementById('btnRequestQuoteGeneral');
        if (sidebarBtn) {
            sidebarBtn.click();
        } else {
            console.error('❌ Request Quote button not found in sidebar');
        }
    };

    // Call sync whenever price might change
    document.addEventListener('priceCalculated', window.syncPriceToBottomBar);
    document.addEventListener('priceUpdated', window.syncPriceToBottomBar);

    function initControls() {
        const controlBar = document.getElementById('mainControlBar');
        if (!controlBar) {
            console.error('❌ Control bar not found!');
            return;
        }

        console.log('✅ Control bar found');

        // State
        let gridVisible = true;
        let autoRotateEnabled = false; // Start disabled until file is uploaded
        let modelRepaired = false;
        let holesFilled = false;

        // Measurement Tool
        const measureToolBtn = document.getElementById('measureToolBtnMain');
        const measurementPanel = document.getElementById('measurementPanel');
        const closeMeasurementPanel = document.getElementById('closeMeasurementPanel');
        const clearMeasurementsBtn = document.getElementById('clearMeasurements');

        if (measureToolBtn) {
            measureToolBtn.addEventListener('click', function(e) {
                e.stopPropagation(); // Prevent click from reaching document mousedown handler

                measurementMode = !measurementMode;
                this.classList.toggle('active', measurementMode);
                measurementPanel.classList.toggle('active', measurementMode);

                const viewer = window.viewerGeneral || window.viewerMedical;

                if (measurementMode) {
                    console.log('📏 Measurement mode activated');
                    console.log('   Viewer:', viewer ? 'Available ✅' : 'Not available ❌');
                    console.log('   Canvas:', viewer && viewer.renderer ? 'Available ✅' : 'Not available ❌');
                    console.log('   measurementMode variable:', measurementMode);
                    console.log('   panMode variable:', panMode);

                    // Disable pan mode when measurement is active
                    if (panMode) {
                        panMode = false;
                        const panToolBtn = document.getElementById('panToolBtnMain');
                        if (panToolBtn) {
                            panToolBtn.classList.remove('active');
                        }
                        if (viewer && viewer.renderer && viewer.renderer.domElement) {
                            viewer.renderer.domElement.style.cursor = 'default';
                        }
                        console.log('👋 Pan mode disabled by measurement mode');
                    }

                    // Disable auto-rotation when measuring
                    stopAutoRotation();
                    const autoRotateBtn = document.getElementById('autoRotateBtnMain');
                    if (autoRotateBtn) {
                        autoRotateBtn.classList.remove('active');
                    }
                    autoRotateEnabled = false;

                    // Keep orbit controls enabled but reduce sensitivity
                    if (viewer && viewer.controls) {
                        viewer.controls.enableRotate = true;
                        viewer.controls.enablePan = true;
                        viewer.controls.enableZoom = true;
                    }
                } else {
                    console.log('📏 Measurement mode deactivated');
                    clearMeasurementData();
                }
            });
        }

        if (closeMeasurementPanel) {
            closeMeasurementPanel.addEventListener('click', function() {
                measurementMode = false;
                measureToolBtn.classList.remove('active');
                measurementPanel.classList.remove('active');
                clearMeasurementData();
            });
        }

        if (clearMeasurementsBtn) {
            clearMeasurementsBtn.addEventListener('click', function() {
                clearMeasurementData();
                const resultEl = document.getElementById('measurementResult');
                if (resultEl) resultEl.style.display = 'none';
            });
        }

        function clearMeasurementData() {
            measurementPoints = [];

            const viewer = window.viewerGeneral || window.viewerMedical;
            if (!viewer || !viewer.scene) return;

            // Remove markers
            measurementMarkers.forEach(marker => {
                viewer.scene.remove(marker);
                if (marker.geometry) marker.geometry.dispose();
                if (marker.material) marker.material.dispose();
            });
            measurementMarkers = [];

            // Remove line
            if (measurementLine) {
                viewer.scene.remove(measurementLine);
                if (measurementLine.geometry) measurementLine.geometry.dispose();
                if (measurementLine.material) measurementLine.material.dispose();
                measurementLine = null;
            }
        }

        // Handle clicks on the 3D model for measurement
        // Make it globally accessible
        window.setupMeasurementClickHandler = function() {
            // Prevent duplicate event listeners
            if (measurementClickHandlerSet) {
                console.log('⚠️ Measurement click handler already set, skipping');
                return;
            }

            const viewer = window.viewerGeneral || window.viewerMedical;
            if (!viewer || !viewer.renderer) {
                console.warn('⚠️ Viewer or renderer not available for measurement setup');
                return;
            }

            const canvas = viewer.renderer.domElement;
            console.log('✅ Setting up measurement click handler on canvas');

            canvas.addEventListener('click', function(event) {
                if (!measurementMode) {
                    // Don't log for every click, too spammy
                    return;
                }

                console.log('🖱️ Canvas clicked in measurement mode');

                if (!window.THREE) {
                    console.error('❌ THREE.js not loaded');
                    return;
                }

                const rect = canvas.getBoundingClientRect();
                const mouse = new THREE.Vector2(
                    ((event.clientX - rect.left) / rect.width) * 2 - 1,
                    -((event.clientY - rect.top) / rect.height) * 2 + 1
                );

                const raycaster = new THREE.Raycaster();
                raycaster.setFromCamera(mouse, viewer.camera);

                // Find all meshes from uploaded files AND modelGroup
                const meshes = [];

                // Method 1: Get from uploadedFiles array
                if (viewer.uploadedFiles && viewer.uploadedFiles.length > 0) {
                    viewer.uploadedFiles.forEach(fileData => {
                        if (fileData.mesh && fileData.mesh instanceof THREE.Mesh) {
                            meshes.push(fileData.mesh);
                        }
                    });
                }

                // Method 2: Traverse the entire scene to find all meshes
                if (meshes.length === 0) {
                    viewer.scene.traverse((child) => {
                        if (child instanceof THREE.Mesh && child.geometry && child.geometry.attributes.position) {
                            // Skip grid and other helpers
                            if (child.name !== 'grid' && !child.name.includes('helper')) {
                                meshes.push(child);
                            }
                        }
                    });
                }

                console.log('🎯 Click raycasting against', meshes.length, 'meshes');

                const intersects = raycaster.intersectObjects(meshes, true);

                if (intersects.length > 0) {
                    const point = intersects[0].point;

                    // If measurement is complete (2 points) or starting fresh, clear and start new
                    if (measurementPoints.length === 0 || measurementPoints.length === 2) {
                        clearMeasurementData();
                        const resultEl = document.getElementById('measurementResult');
                        if (resultEl) resultEl.style.display = 'none';
                        console.log('🧹 Starting new measurement');
                    }

                    measurementPoints.push(point);

                    // Create marker sphere
                    const markerGeometry = new THREE.SphereGeometry(1, 16, 16);
                    const markerMaterial = new THREE.MeshBasicMaterial({
                        color: measurementPoints.length === 1 ? 0xff6b35 : 0x4caf50
                    });
                    const marker = new THREE.Mesh(markerGeometry, markerMaterial);
                    marker.position.copy(point);
                    viewer.scene.add(marker);
                    measurementMarkers.push(marker);

                    console.log(`📍 Point ${measurementPoints.length}:`, point);

                    // Update UI (with null checks)
                    const coordStr = `(${point.x.toFixed(2)}, ${point.y.toFixed(2)}, ${point.z.toFixed(2)})`;
                    if (measurementPoints.length === 1) {
                        const point1El = document.getElementById('point1Coords');
                        if (point1El) point1El.textContent = coordStr;
                    } else if (measurementPoints.length === 2) {
                        const point2El = document.getElementById('point2Coords');
                        if (point2El) point2El.textContent = coordStr;

                        // Calculate distance
                        const distance = measurementPoints[0].distanceTo(measurementPoints[1]);
                        const distanceEl = document.getElementById('distanceValue');
                        const thicknessEl = document.getElementById('thicknessValue');
                        const resultEl = document.getElementById('measurementResult');

                        // Update whichever element exists
                        if (distanceEl) distanceEl.textContent = distance.toFixed(2) + ' mm';
                        if (thicknessEl) thicknessEl.textContent = distance.toFixed(2) + ' mm';
                        if (resultEl) resultEl.style.display = 'block';

                        // Draw line between points - BLUE COLOR
                        const lineGeometry = new THREE.BufferGeometry().setFromPoints(measurementPoints);
                        const lineMaterial = new THREE.LineBasicMaterial({
                            color: 0x0088ff, // Blue color
                            linewidth: 3
                        });
                        measurementLine = new THREE.Line(lineGeometry, lineMaterial);
                        viewer.scene.add(measurementLine);

                        console.log('📏 Distance:', distance.toFixed(2), 'mm');

                        // DON'T reset measurementPoints - keep the measurement displayed
                        // It will be cleared when user clicks for a new measurement
                        // setTimeout(() => {
                        //     measurementPoints = [];
                        //     const p1El = document.getElementById('point1Coords');
                        //     const p2El = document.getElementById('point2Coords');
                        //     if (p1El) p1El.textContent = '-';
                        //     if (p2El) p2El.textContent = '-';
                        // }, 100);
                    }
                }
            });

            // Add hover handler for live measurement display
            canvas.addEventListener('mousemove', function(event) {
                if (!measurementMode) return;

                if (!window.THREE) {
                    console.error('❌ THREE.js not loaded');
                    return;
                }

                const rect = canvas.getBoundingClientRect();
                const mouse = new THREE.Vector2(
                    ((event.clientX - rect.left) / rect.width) * 2 - 1,
                    -((event.clientY - rect.top) / rect.height) * 2 + 1
                );

                const raycaster = new THREE.Raycaster();
                // Increase threshold for better intersection detection with fine details
                raycaster.params.Points.threshold = 0.5;
                raycaster.params.Line.threshold = 1;
                raycaster.setFromCamera(mouse, viewer.camera);                // Find all meshes from uploaded files AND modelGroup
                const meshes = [];

                // Method 1: Get from uploadedFiles array
                if (viewer.uploadedFiles && viewer.uploadedFiles.length > 0) {
                    viewer.uploadedFiles.forEach(fileData => {
                        if (fileData.mesh && fileData.mesh instanceof THREE.Mesh) {
                            meshes.push(fileData.mesh);
                        }
                    });
                }

                // Method 2: Traverse the entire scene to find all meshes
                if (meshes.length === 0) {
                    viewer.scene.traverse((child) => {
                        if (child instanceof THREE.Mesh && child.geometry && child.geometry.attributes.position) {
                            // Skip grid and other helpers
                            if (child.name !== 'grid' && !child.name.includes('helper')) {
                                meshes.push(child);
                            }
                        }
                    });
                }

                // Removed spammy log here - fires on every mousemove
                const intersects = raycaster.intersectObjects(meshes, true);

                const thicknessValueEl = document.getElementById('thicknessValue');

                // If measurement is complete (2 points), don't update - keep the value displayed
                if (measurementPoints.length === 2) {
                    return; // Measurement complete, don't overwrite
                }

                if (intersects.length > 0) {
                    const point = intersects[0].point;

                    // If we have one point already, show distance to hover point (live preview)
                    if (measurementPoints.length === 1) {
                        const distance = measurementPoints[0].distanceTo(point);

                        // Format distance with appropriate precision
                        let formattedDistance;
                        if (distance < 1) {
                            formattedDistance = distance.toFixed(3); // Sub-millimeter precision
                        } else if (distance < 10) {
                            formattedDistance = distance.toFixed(2); // 0.01mm precision
                        } else {
                            formattedDistance = distance.toFixed(1); // 0.1mm precision
                        }

                        if (thicknessValueEl) thicknessValueEl.textContent = formattedDistance + ' mm';
                    } else if (measurementPoints.length === 0) {
                        // Show placeholder
                        if (thicknessValueEl) thicknessValueEl.textContent = '-';
                    }
                } else if (measurementPoints.length === 0) {
                    // Only reset if no measurement in progress
                    if (thicknessValueEl) thicknessValueEl.textContent = '-';
                }
            });

            measurementClickHandlerSet = true;
            console.log('✅ Measurement click handler setup complete');
        }

        // Camera buttons
        document.querySelectorAll('.camera-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                console.log('📷 Camera view:', this.dataset.view);
                document.querySelectorAll('.camera-btn').forEach(b => b.classList.remove('active'));
                this.classList.add('active');
                setCameraView(this.dataset.view);
            });
        });

        // Grid toggle
        const gridBtn = document.getElementById('toggleGridBtnMain');
        if (gridBtn) {
            gridBtn.addEventListener('click', function() {
                gridVisible = !gridVisible;
                this.classList.toggle('active', gridVisible);
                console.log('🎨 Grid:', gridVisible);
                toggleGrid(gridVisible);
            });
        }

        // Auto-rotate
        const rotateBtn = document.getElementById('autoRotateBtnMain');
        if (rotateBtn) {
            rotateBtn.addEventListener('click', function() {
                // Check if a file has been uploaded
                const viewer = window.viewerGeneral || window.viewerMedical;
                if (!viewer || !viewer.uploadedFiles || viewer.uploadedFiles.length === 0) {
                    console.warn('⚠️ Cannot enable auto-rotate: No file uploaded');
                    return;
                }

                autoRotateEnabled = !autoRotateEnabled;
                this.classList.toggle('active', autoRotateEnabled);
                console.log('🔄 Auto-rotate:', autoRotateEnabled);
                if (autoRotateEnabled) {
                    startAutoRotation();
                } else {
                    stopAutoRotation();
                }
            });
        }

        // ============================================
        // PAN TOOL - MOVED TO QUOTE-VIEWER.BLADE.PHP TOP TOOLBAR
        // The Move/Pan functionality is now in the professional toolbar
        // in quote-viewer.blade.php as part of window.toolbarHandler
        // ============================================
        /*
        // Pan Tool - Drag to move model (variables declared at top level)
        const panToolBtn = document.getElementById('panToolBtnMain');
        console.log('🔍 Pan button found:', panToolBtn ? 'YES ✅' : 'NO ❌');

        // Store canvas handlers so we can remove them later
        let canvasPanHandlers = null;

        if (panToolBtn) {
            ... [CODE REMOVED - NOW IN QUOTE-VIEWER.BLADE.PHP] ...
        }

        // Canvas-specific pan handlers
        function handleCanvasMouseDown(e) { ... }
        function handleCanvasMouseMove(e) { ... }
        function handleCanvasMouseUp(e) { ... }
        */

        // ============================================
        // SAVE & CALCULATE BUTTON - Connect to EnhancedSaveCalculate module
        // VERSION: DEC-23-2025-V2-CACHEBUSTER-{{ time() }}
        // ============================================

        console.log('🔥🔥🔥 QUOTE.BLADE.PHP SCRIPT LOADED - NEW VERSION DEC-23-2025-V2 🔥🔥🔥');
        console.log('🔥 Timestamp:', new Date().toISOString());
        console.log('🔥 If you see this with V2, the NEW code is loaded!');
        console.log('🔥 If you do NOT see V2, press CTRL + SHIFT + DELETE and clear cache!');

        // Wait for EnhancedSaveCalculate to be loaded
        function initSaveCalculateButton() {
            const saveBtn = document.getElementById('saveCalculationsBtnMain');
            if (!saveBtn) {
                console.warn('⚠️ Save & Calculate button not found');
                return;
            }

            if (!window.EnhancedSaveCalculate) {
                console.warn('⚠️ EnhancedSaveCalculate not loaded yet, will retry...');
                setTimeout(initSaveCalculateButton, 500);
                return;
            }

            console.log('✅ Connecting Save & Calculate button to EnhancedSaveCalculate module...');
            console.log('✅ EnhancedSaveCalculate version:', window.EnhancedSaveCalculate.version);

            saveBtn.addEventListener('click', async function() {
                console.log('💾💾💾 SAVE & CALCULATE CLICKED - NEW HANDLER V2 💾💾💾');
                console.log('💾 Using EnhancedSaveCalculate v' + window.EnhancedSaveCalculate.version);
                console.log('💾 This is the NEW code that calls EnhancedSaveCalculate.execute()');

                // Show loading state
                const originalHTML = this.innerHTML;
                this.innerHTML = '<svg width="18" height="18" viewBox="0 0 18 18" fill="none" class="spin-icon"><circle cx="9" cy="9" r="7" stroke="currentColor" stroke-width="1.5" stroke-dasharray="40"/></svg><span>Processing...</span>';
                this.style.pointerEvents = 'none';

                try {
                    // Call the enhanced save & calculate
                    console.log('📞 Calling EnhancedSaveCalculate.execute("general")...');
                    await window.EnhancedSaveCalculate.execute('general');
                    console.log('✅ EnhancedSaveCalculate.execute() completed successfully');
                } catch (error) {
                    console.error('❌ Save & Calculate error:', error);
                    console.error('❌ Error stack:', error.stack);
                } finally {
                    // Restore button
                    setTimeout(() => {
                        this.innerHTML = originalHTML;
                        this.style.pointerEvents = '';
                    }, 1000);
                }
            });

            console.log('✅✅✅ Save & Calculate button connected successfully to NEW handler! ✅✅✅');
        }

        // Initialize when page loads
        initSaveCalculateButton();

        // System health check - runs after page load
        window.addEventListener('load', function() {
            setTimeout(() => {
                console.log('\n🔍 ========== SYSTEM HEALTH CHECK ==========');
                console.log('📋 Core Components:');
                console.log('   ✓ viewerGeneral:', !!window.viewerGeneral);
                console.log('   ✓ viewerMedical:', !!window.viewerMedical);
                console.log('   ✓ fileManagerGeneral:', !!window.fileManagerGeneral);
                console.log('   ✓ FileManager class:', !!window.FileManager);

                console.log('\n📋 Functions Available:');
                console.log('   ✓ showAllFilePrices:', !!window.showAllFilePrices);
                console.log('   ✓ calculateFilePrice:', !!window.calculateFilePrice);
                console.log('   ✓ updateModelDimensions:', !!updateModelDimensions);

                console.log('\n📋 UI Elements:');
                console.log('   ✓ Save button:', !!document.getElementById('saveCalculationsBtnMain'));
                console.log('   ✓ Price summary:', !!document.getElementById('priceSummaryGeneral'));
                console.log('   ✓ Total volume:', !!document.getElementById('quoteTotalVolumeGeneral'));
                console.log('   ✓ Total price:', !!document.getElementById('quoteTotalPriceGeneral'));

                // Check if viewer has required methods
                const viewer = window.viewerGeneral || window.viewerMedical;
                if (viewer) {
                    console.log('\n📋 Viewer Methods:');
                    console.log('   ✓ calculatePrice:', typeof viewer.calculatePrice === 'function');
                    console.log('   ✓ calculateVolume:', typeof viewer.calculateVolume === 'function');
                    console.log('   ✓ repairModel:', typeof viewer.repairModel === 'function');
                    console.log('   ✓ fillHoles:', typeof viewer.fillHoles === 'function');
                }

                console.log('==========================================\n');

                // Warn about missing components
                const issues = [];
                if (!window.FileManager) issues.push('FileManager class missing - check if 3d-file-manager.js is loaded');
                if (!window.viewerGeneral && !window.viewerMedical) issues.push('No viewer initialized');
                if (viewer && typeof viewer.calculatePrice !== 'function') issues.push('viewer.calculatePrice() method missing');

                if (issues.length > 0) {
                    console.warn('⚠️ ISSUES DETECTED:');
                    issues.forEach(issue => console.warn('   - ' + issue));
                } else {
                    console.log('✅ All systems ready! Upload a file and click "Save & Calculate"');
                }
            }, 1500);
        });

        // Helper functions for THREE.JS
        function setCameraView(view) {
            console.log('📷 Setting camera to:', view);
            const viewer = window.viewerGeneral || window.viewerMedical;
            if (!viewer || !viewer.camera || !viewer.controls) {
                console.log('⚠️ Viewer, camera or controls not ready');
                return;
            }

            const camera = viewer.camera;
            const controls = viewer.controls;
            const target = controls.target || new THREE.Vector3(0, 0, 0);
            const distance = camera.position.distanceTo(target);

            // Reset view - return to default 180° position
            if (view === 'reset') {
                camera.position.set(100, 100, 200);
                controls.target.set(0, 0, 0);
                controls.update();
                console.log('✅ Camera reset to default position');
                return;
            }

            const positions = {
                top: { x: 0, y: distance, z: 0 },
                bottom: { x: 0, y: -distance, z: 0 },
                front: { x: 0, y: 0, z: distance },
                back: { x: 0, y: 0, z: -distance },
                right: { x: distance, y: 0, z: 0 },
                left: { x: -distance, y: 0, z: 0 }
            };

            if (positions[view]) {
                camera.position.set(
                    target.x + positions[view].x,
                    target.y + positions[view].y,
                    target.z + positions[view].z
                );
                controls.update();
                console.log('✅ Camera moved to', view);
            }
        }

        function toggleGrid(visible) {
            console.log('🎨 Toggling grid:', visible);
            const viewer = window.viewerGeneral || window.viewerMedical;
            if (!viewer || !viewer.scene) {
                console.log('⚠️ No scene found');
                return;
            }

            viewer.scene.traverse(function(object) {
                if (object.name === 'grid' || object.type === 'GridHelper') {
                    object.visible = visible;
                    console.log('✅ Grid visibility:', visible);
                }
            });
        }

        function startAutoRotation() {
            console.log('🔄 Starting auto-rotation');
            const viewer = window.viewerGeneral || window.viewerMedical;
            if (!viewer || !viewer.controls) {
                console.log('⚠️ No controls found');
                return;
            }

            if (viewer.controls.autoRotate !== undefined) {
                viewer.controls.autoRotate = true;
                viewer.controls.autoRotateSpeed = 2.0;
                // Force update controls
                viewer.controls.update();
                console.log('✅ Auto-rotation enabled, state:', viewer.controls.autoRotate);
            }
        }

        function stopAutoRotation() {
            console.log('🛑 Stopping auto-rotation');
            const viewer = window.viewerGeneral || window.viewerMedical;
            if (!viewer || !viewer.controls) {
                console.log('⚠️ No viewer or controls found');
                return;
            }

            if (viewer.controls.autoRotate !== undefined) {
                viewer.controls.autoRotate = false;
                // Force update controls
                viewer.controls.update();
                console.log('✅ Auto-rotation disabled, state:', viewer.controls.autoRotate);
            }
        }

        console.log('✅✅✅ Control bar initialized for THREE.JS!');
    }

    // Update model dimensions in info panel
    function updateModelDimensions() {
        const viewer = window.viewerGeneral || window.viewerMedical;
        if (!viewer || !viewer.uploadedFiles || viewer.uploadedFiles.length === 0) {
            console.log('⚠️ No files uploaded yet');
            return;
        }

        const file = viewer.uploadedFiles[0];
        console.log('📏 Updating model dimensions from file:', file);

        // Calculate bounding box from geometry if not already calculated
        let sizeX = 0, sizeY = 0, sizeZ = 0;

        if (file.geometry) {
            if (!file.geometry.boundingBox) {
                file.geometry.computeBoundingBox();
            }

            const box = file.geometry.boundingBox;
            const size = new THREE.Vector3();
            box.getSize(size);

            sizeX = size.x;
            sizeY = size.y;
            sizeZ = size.z;

            console.log('📦 Calculated from geometry:', { x: sizeX, y: sizeY, z: sizeZ });
        } else {
            // Fallback to stored values
            sizeX = file.boundingBox?.x || file.size?.x || 0;
            sizeY = file.boundingBox?.y || file.size?.y || 0;
            sizeZ = file.boundingBox?.z || file.size?.z || 0;
        }

        // Handle volume - it can be an object {mm3, cm3} or a number
        let volumeCM3 = 0;
        if (file.volume) {
            if (typeof file.volume === 'object' && file.volume.cm3) {
                volumeCM3 = file.volume.cm3;
            } else if (typeof file.volume === 'number') {
                volumeCM3 = file.volume / 1000; // Convert mm³ to cm³
            }
        }

        // Update measurement panel dimensions
        const modelWidth = document.getElementById('modelWidth');
        const modelHeight = document.getElementById('modelHeight');
        const modelDepth = document.getElementById('modelDepth');

        if (modelWidth) modelWidth.textContent = sizeX.toFixed(2) + ' mm';
        if (modelHeight) modelHeight.textContent = sizeY.toFixed(2) + ' mm';
        if (modelDepth) modelDepth.textContent = sizeZ.toFixed(2) + ' mm';

        console.log('✅ Model dimensions updated:', { width: sizeX, height: sizeY, depth: sizeZ, volume: volumeCM3 });
    }

    // Set default camera rotation to 180 degrees when model loads
    let isRestoredSession = false; // Track if this is a restored session

    window.addEventListener('modelLoaded', function(event) {
        console.log('🎨 Model loaded event triggered');
        const viewer = window.viewerGeneral || window.viewerMedical;

        // Enable auto-rotate button now that a file is uploaded
        const rotateBtn = document.getElementById('autoRotateBtnMain');
        if (rotateBtn) {
            rotateBtn.disabled = false;
            rotateBtn.style.opacity = '1';
            rotateBtn.style.cursor = 'pointer';
            rotateBtn.title = 'Auto-rotate model';
            console.log('✅ Auto-rotate button enabled');
        }

        if (viewer && viewer.camera && viewer.controls) {
            // Only rotate for NEW uploads (not restored sessions)
            if (!isRestoredSession) {
                console.log('🆕 New file upload detected - applying default view');

                // Rotate camera 180 degrees around Y axis
                const camera = viewer.camera;
                const controls = viewer.controls;
                const target = controls.target || new THREE.Vector3(0, 0, 0);
                const distance = camera.position.distanceTo(target);

                // Position camera on opposite side (180°)
                camera.position.set(target.x, target.y, target.z - distance);
                controls.update();

                // Don't start auto-rotation automatically - let user control it
                if (controls.autoRotate !== undefined) {
                    controls.autoRotate = false;
                    console.log('✅ Camera rotated 180°, auto-rotate disabled by default');
                }
            } else {
                console.log('🔄 Restored session - keeping saved camera state');
                // Reset flag for next upload
                isRestoredSession = false;
            }
        }

        // Update model dimensions when model loads
        setTimeout(updateModelDimensions, 500);

        // Setup measurement click handler
        setupMeasurementClickHandler();
    });

    // Listen for pricing updates (when measurements change)
    window.addEventListener('pricingUpdateNeeded', function(event) {
        console.log('💰 Pricing update needed, refreshing model dimensions');
        updateModelDimensions();
    });

    // ============================================
    // FILE STORAGE & SHARE SYSTEM INTEGRATION
    // ============================================

    // Initialize IndexedDB on page load
    window.addEventListener('load', async function() {
        console.log('💾 Initializing File Storage Manager...');
        try {
            await window.fileStorageManager.init();
            console.log('✅ File Storage Manager initialized');

            // Check for shared file in URL
            const fileId = window.fileStorageManager.getFileIdFromURL();

            // Validate file ID - must be non-null and start with 'file_'
            if (fileId && fileId !== 'null' && fileId !== 'undefined' && fileId.startsWith('file_')) {
                console.log('🔗 Loading shared file from URL:', fileId);
                await loadSharedFile(fileId);
            } else {
                if (fileId && fileId !== 'null' && fileId !== 'undefined') {
                    console.warn('⚠️ Invalid file ID in URL:', fileId);
                    // Clean up invalid URL parameter
                    const url = new URL(window.location.href);
                    url.searchParams.delete('file');
                    window.history.replaceState({}, '', url.toString());
                }

                // No valid URL parameter - check for last uploaded file
                console.log('🔍 Checking for last uploaded file...');
                await loadLastUploadedFile();
            }
        } catch (error) {
            console.error('❌ Failed to initialize storage:', error);
        }
    });

    // Save file to IndexedDB when uploaded
    window.addEventListener('modelLoaded', async function(event) {
        try {
            // Don't save if this is a restored session (file already in storage)
            if (isRestoredSession) {
                console.log('ℹ️ Skipping save - restored session');
                return;
            }

            const viewer = window.viewerGeneral || window.viewerMedical;
            if (!viewer || !viewer.uploadedFiles || viewer.uploadedFiles.length === 0) {
                console.warn('⚠️ No uploaded files found in viewer');
                return;
            }

            const uploadedFile = viewer.uploadedFiles[0];
            if (!uploadedFile || !uploadedFile.file) {
                console.warn('⚠️ Uploaded file or file object is missing');
                return;
            }

            console.log('💾 Preparing to save file:', uploadedFile.file.name);
            console.log('   File size:', (uploadedFile.file.size / 1024 / 1024).toFixed(2), 'MB');

            // Show saving notification
            showNotification('💾 Saving file to browser storage...', 'info');

            // Convert File to ArrayBuffer for IndexedDB storage
            const arrayBuffer = await uploadedFile.file.arrayBuffer();
            console.log('   Converted to ArrayBuffer');

            // Save file to IndexedDB with ArrayBuffer
            const fileId = await window.fileStorageManager.saveFile(
                arrayBuffer,
                uploadedFile.file.name,
                uploadedFile.geometry,
                uploadedFile.mesh
            );

            if (!fileId || fileId === 'null' || fileId === 'undefined' || !fileId.startsWith('file_')) {
                console.error('❌ Failed to generate valid file ID:', fileId);
                showNotification('❌ Failed to save file to storage', 'error');
                return;
            }

            console.log('✅ File saved to browser storage:', fileId);
            console.log('   Storage ID set as currentFileId');

            // Ensure the viewer's uploaded file entry knows its storage ID
            try {
                if (uploadedFile) {
                    uploadedFile.storageId = fileId;

                    if (viewer && Array.isArray(viewer.uploadedFiles)) {
                        const fileIndex = viewer.uploadedFiles.indexOf(uploadedFile);
                        if (fileIndex !== -1) {
                            viewer.uploadedFiles[fileIndex].storageId = fileId;
                        }
                    }

                    console.log('   storageId attached to uploaded file:', fileId);
                }
            } catch (attachError) {
                console.warn('⚠️ Could not attach storageId to uploaded file:', attachError);
            }

            // Show success notification
            showNotification('✅ File saved successfully!', 'success');

            // Auto-save camera state every 30 seconds
            startAutoSave(fileId);

        } catch (error) {
            console.error('❌ Failed to save file:', error);
            showNotification('❌ Failed to save file: ' + error.message, 'error');
        }
    });

    // Share button functionality
    const shareBtnMain = document.getElementById('shareBtnMain');
    if (shareBtnMain) {
        // Initially disable share button until file is saved via Save & Calculate
        shareBtnMain.disabled = true;
        shareBtnMain.style.opacity = '0.5';
        shareBtnMain.style.cursor = 'not-allowed';
        shareBtnMain.title = 'Save & Calculate first to enable sharing';

        shareBtnMain.addEventListener('click', async function() {
            try {
                // Get file ID from URL (set by Save & Calculate)
                const urlParams = new URLSearchParams(window.location.search);
                let fileId = urlParams.get('files');

                console.log('🔍 Share button clicked - File ID from URL:', fileId);

                // Validate file ID
                if (!fileId || fileId === 'null' || fileId === 'undefined' || !fileId.startsWith('file_')) {
                    showNotification('⚠️ Please click "Save & Calculate" first to enable sharing', 'warning');
                    return;
                }

                // Save current camera state before sharing
                await saveCameraState();

                // Open share modal with file ID from URL (no duplicate save)
                if (window.shareModal && typeof window.shareModal.open === 'function') {
                    await window.shareModal.open(fileId);
                    console.log('🔗 Share modal opened with file ID:', fileId);
                } else {
                    console.error('❌ Share modal not available');
                    showNotification('❌ Share feature is not available', 'error');
                }
            } catch (error) {
                console.error('❌ Share button error:', error);
                showNotification('❌ Failed to share: ' + error.message, 'error');
            }
        });

        // Listen for URL changes to enable/disable share button
        window.addEventListener('urlUpdated', function() {
            const urlParams = new URLSearchParams(window.location.search);
            const fileId = urlParams.get('files');

            if (fileId && fileId.startsWith('file_')) {
                shareBtnMain.disabled = false;
                shareBtnMain.style.opacity = '1';
                shareBtnMain.style.cursor = 'pointer';
                shareBtnMain.title = 'Share this 3D model';
                console.log('✅ Share button enabled with file ID:', fileId);
            }
        });
    }

    // Auto-save camera state every 30 seconds
    let autoSaveInterval = null;
    function startAutoSave(fileId) {
        if (autoSaveInterval) {
            clearInterval(autoSaveInterval);
        }

        autoSaveInterval = setInterval(async () => {
            await saveCameraState();
        }, 30000); // 30 seconds

        console.log('💾 Auto-save started (every 30 seconds)');
    }

    // Save current camera state
    async function saveCameraState() {
        const viewer = window.viewerGeneral || window.viewerMedical;
        if (!viewer || !viewer.camera || !viewer.controls) return false;

        const cameraData = {
            position: {
                x: viewer.camera.position.x,
                y: viewer.camera.position.y,
                z: viewer.camera.position.z
            },
            rotation: {
                x: viewer.camera.rotation.x,
                y: viewer.camera.rotation.y,
                z: viewer.camera.rotation.z
            },
            zoom: viewer.camera.zoom,
            target: viewer.controls.target ? {
                x: viewer.controls.target.x,
                y: viewer.controls.target.y,
                z: viewer.controls.target.z
            } : null
        };

        return await window.fileStorageManager.saveCameraState(cameraData);
    }

    // Load shared file from IndexedDB
    async function loadSharedFile(fileId) {
        try {
            console.log('🔍 Loading shared file:', fileId);
            const fileRecord = await window.fileStorageManager.loadFile(fileId);

            if (!fileRecord) {
                showNotification('⚠️ File not found or expired', 'error');
                console.error('❌ File record not found in storage');
                // Clear invalid URL parameter
                const url = new URL(window.location.href);
                url.searchParams.delete('file');
                window.history.replaceState({}, '', url.toString());
                return;
            }

            console.log('✅ File record found:', fileRecord.fileName);
            console.log('   File size:', (fileRecord.metadata?.fileSize / 1024 / 1024).toFixed(2), 'MB');

            // Mark as restored session to prevent auto-rotate
            isRestoredSession = true;

            // Show loading notification
            showNotification('📥 Loading shared 3D model...', 'info');

            // Create File object from stored data
            const file = new File([fileRecord.fileData], fileRecord.fileName, {
                type: 'application/octet-stream'
            });

            // Wait for viewer to be fully initialized
            let viewer = window.viewerGeneral || window.viewerMedical;
            let attempts = 0;
            while (!viewer && attempts < 20) {
                await new Promise(resolve => setTimeout(resolve, 100));
                viewer = window.viewerGeneral || window.viewerMedical;
                attempts++;
            }

            if (!viewer) {
                console.error('❌ Viewer not initialized');
                showNotification('❌ Failed to load file: Viewer not ready', 'error');
                isRestoredSession = false;
                return;
            }

            // Trigger file upload
            console.log('📤 Loading file into viewer...');
            const storageId = fileRecord.id || fileId;
            await viewer.loadFile(file, storageId);

            // Wait for model to be fully loaded
            await new Promise(resolve => setTimeout(resolve, 1000));

            // Setup measurement handler for loaded file
            console.log('🎯 Setting up measurement handlers for shared file...');
            setupMeasurementClickHandler();

            // Restore camera state
            if (fileRecord.edits && fileRecord.edits.camera) {
                console.log('📷 Restoring camera state...');
                setTimeout(() => {
                    restoreCameraState(fileRecord.edits.camera);
                }, 500);
            }

            // Set current file ID
            window.fileStorageManager.currentFileId = storageId;

            showNotification('✅ Shared model loaded successfully!', 'success');
            console.log('✅ Shared file loaded successfully');
            console.log('   Current file ID set to:', storageId);

            // Restart auto-save
            startAutoSave(storageId);
        } catch (error) {
            console.error('❌ Failed to load shared file:', error);
            showNotification('❌ Failed to load shared file: ' + error.message, 'error');
            isRestoredSession = false; // Reset on error

            // Clear invalid URL parameter
            const url = new URL(window.location.href);
            url.searchParams.delete('file');
            window.history.replaceState({}, '', url.toString());
        }
    }

    // Restore camera state from saved data
    function restoreCameraState(cameraData) {
        const viewer = window.viewerGeneral || window.viewerMedical;
        if (!viewer || !viewer.camera || !viewer.controls) {
            console.warn('⚠️ Cannot restore camera: Viewer not ready');
            return;
        }

        if (!cameraData || !cameraData.position) {
            console.warn('⚠️ Invalid camera data');
            return;
        }

        try {
            console.log('📷 Restoring camera state...');
            console.log('   Position:', cameraData.position);
            console.log('   Target:', cameraData.target);

            viewer.camera.position.set(
                cameraData.position.x,
                cameraData.position.y,
                cameraData.position.z
            );

            if (cameraData.rotation) {
                viewer.camera.rotation.set(
                    cameraData.rotation.x,
                    cameraData.rotation.y,
                    cameraData.rotation.z
                );
            }

            if (cameraData.target && viewer.controls.target) {
                viewer.controls.target.set(
                    cameraData.target.x,
                    cameraData.target.y,
                    cameraData.target.z
                );
            }

            if (cameraData.zoom && cameraData.zoom > 0) {
                viewer.camera.zoom = cameraData.zoom;
                viewer.camera.updateProjectionMatrix();
            }

            viewer.controls.update();
            if (viewer.renderer && viewer.scene) {
                viewer.renderer.render(viewer.scene, viewer.camera);
            }
            console.log('✅ Camera state restored successfully');
        } catch (error) {
            console.error('❌ Failed to restore camera state:', error);
        }
    }

    // Load last uploaded file (for page refresh persistence)
    async function loadLastUploadedFile() {
        try {
            const allFiles = await window.fileStorageManager.getAllFiles();

            if (!allFiles || allFiles.length === 0) {
                console.log('📭 No files found in storage');
                return;
            }

            // Sort by upload time (most recent first)
            allFiles.sort((a, b) => b.uploadTime - a.uploadTime);
            const lastFile = allFiles[0];

            console.log('📂 Found last uploaded file:', lastFile.fileName);
            console.log('   File ID:', lastFile.id);
            console.log('   Upload time:', new Date(lastFile.uploadTime).toLocaleString());

            // Mark as restored session to prevent auto-rotate
            isRestoredSession = true;

            // Show loading notification
            showNotification('📥 Restoring your last session...', 'info');

            // Create File object from stored data
            const file = new File([lastFile.fileData], lastFile.fileName, {
                type: 'application/octet-stream'
            });

            // Wait for viewer to be fully initialized
            let viewer = window.viewerGeneral || window.viewerMedical;
            let attempts = 0;
            while (!viewer && attempts < 20) {
                await new Promise(resolve => setTimeout(resolve, 100));
                viewer = window.viewerGeneral || window.viewerMedical;
                attempts++;
            }

            if (!viewer) {
                console.error('❌ Viewer not initialized');
                showNotification('❌ Failed to restore session: Viewer not ready', 'error');
                isRestoredSession = false;
                return;
            }

            // Trigger file upload
            console.log('📤 Loading file into viewer...');
            await viewer.loadFile(file, lastFile.id);

            // Wait for model to be fully loaded
            await new Promise(resolve => setTimeout(resolve, 1000));

            // Restore camera state
            if (lastFile.edits && lastFile.edits.camera) {
                console.log('📷 Restoring camera state...');
                setTimeout(() => {
                    restoreCameraState(lastFile.edits.camera);
                }, 500);
            }

            // Update URL with file ID and set current file ID
            window.fileStorageManager.currentFileId = lastFile.id;
            window.fileStorageManager.updateURL(lastFile.id);

            showNotification('✅ Session restored successfully!', 'success');
            console.log('✅ Last file loaded successfully');
            console.log('   Current file ID set to:', window.fileStorageManager.currentFileId);

            // Restart auto-save
            startAutoSave(lastFile.id);
        } catch (error) {
            console.error('❌ Failed to load last file:', error);
            isRestoredSession = false; // Reset on error
            // Don't show error notification - user might be starting fresh
        }
    }

    // Save edits (repair, fill holes, etc.)
    window.addEventListener('modelRepaired', async function() {
        await window.fileStorageManager.saveRepair('repair');
        console.log('💾 Repair action saved');
    });

    window.addEventListener('modelHolesFilled', async function() {
        await window.fileStorageManager.saveRepair('fillHoles');
        console.log('💾 Fill holes action saved');
    });

    // Show notification toast
    function showNotification(message, type = 'info') {
        const toast = document.createElement('div');
        toast.className = 'notification-toast';

        const colors = {
            success: 'linear-gradient(135deg, #11998e 0%, #38ef7d 100%)',
            error: 'linear-gradient(135deg, #f093fb 0%, #f5576c 100%)',
            warning: 'linear-gradient(135deg, #f093fb 0%, #f5576c 100%)',
            info: 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)'
        };

        const icons = {
            success: '✓',
            error: '⚠️',
            warning: '⚠️',
            info: 'ℹ️'
        };

        toast.innerHTML = `
            <div style="display: flex; align-items: center; gap: 12px;">
                <span style="font-size: 20px; display: flex; align-items: center; justify-content: center; width: 32px; height: 32px; background: rgba(255,255,255,0.2); border-radius: 50%;">${icons[type]}</span>
                <span>${message.replace(/^[✅✓]\s*/, '')}</span>
            </div>
        `;

        toast.style.cssText = `
            position: fixed;
            top: 20px;
            left: 50%;
            transform: translateX(-50%) translateY(-100%);
            padding: 16px 28px;
            background: ${colors[type] || colors.info};
            color: white;
            border-radius: 12px;
            font-size: 15px;
            font-weight: 500;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.25);
            z-index: 9999;
            animation: notifSlideInTop 0.4s cubic-bezier(0.34, 1.56, 0.64, 1) forwards;
            backdrop-filter: blur(10px);
        `;

        document.body.appendChild(toast);

        setTimeout(() => {
            toast.style.animation = 'notifSlideOutTop 0.3s ease forwards';
            setTimeout(() => toast.remove(), 300);
        }, 3000);
    }

    // Add notification animations
    const notifStyles = document.createElement('style');
    notifStyles.textContent = `
        @keyframes notifSlideInTop {
            from {
                transform: translateX(-50%) translateY(-100%);
                opacity: 0;
            }
            to {
                transform: translateX(-50%) translateY(0);
                opacity: 1;
            }
        }
        @keyframes notifSlideOutTop {
            from {
                transform: translateX(-50%) translateY(0);
                opacity: 1;
            }
            to {
                transform: translateX(-50%) translateY(-150%);
                opacity: 0;
            }
        }
    `;
    document.head.appendChild(notifStyles);
</script>
@endsection
