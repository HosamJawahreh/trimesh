@extends('frontend.layouts.master')

@section('title', '3D Printing Quote - Get Instant Pricing')

@section('meta')
    <meta name="description" content="Upload your 3D files and get instant pricing for your 3D printing projects. Support for STL, OBJ, and PLY files.">
@endsection

@section('header')
    {{-- No header for full-screen quote experience --}}
@endsection

@section('content')
<style>
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
    }

    /* General mode - Blue gradient */
    .quote-viewer.mode-general {
        background: linear-gradient(180deg, #4a90e2 0%, #7ab8f5 50%, #b8d8f7 100%) !important;
    }

    /* Medical mode - Original Shapeways gradient */
    .quote-viewer.mode-medical {
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
    .viewer-bottom-controls {
        position: fixed !important;
        bottom: 20px !important;
        left: 400px !important;
        right: 20px !important;
        width: calc(100vw - 420px) !important;
        height: auto !important;
        background: rgba(255, 255, 255, 0.98) !important;
        backdrop-filter: blur(20px) !important;
        border: 1px solid rgba(184, 198, 219, 0.3) !important;
        border-radius: 12px !important;
        display: flex !important;
        align-items: center !important;
        justify-content: space-between !important;
        padding: 16px 28px !important;
        gap: 20px !important;
        z-index: 999999 !important;
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.12) !important;
        pointer-events: auto !important;
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
                <div id="thicknessValue" style="font-size: 24px; font-weight: 700; color: #333; margin-bottom: 4px;">-</div>
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

    <!-- Sidebar -->
    <div class="quote-sidebar">
        @include('frontend.pages.quote-viewer')
    </div>

    <!-- Viewer Area -->
    <div class="quote-viewer mode-general">
        <!-- The 3D canvas will be inserted here by JavaScript -->
    </div>

    <!-- Control Bar -->
    <div class="viewer-bottom-controls" id="mainControlBar">
        <!-- Camera Views - Compact with hover dropdown -->
        <div class="control-section camera-section">
            <div class="control-label">📹 Camera View</div>
            <div class="camera-grid">
                <button type="button" class="control-btn camera-btn" data-view="top" title="Top View">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none">
                        <path d="M12 5L12 19M12 5L8 9M12 5L16 9" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                    </svg>
                    <span>Top</span>
                </button>
                <button type="button" class="control-btn camera-btn active" data-view="front" title="Front View">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none">
                        <circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="2"/>
                        <circle cx="12" cy="12" r="3" fill="currentColor"/>
                    </svg>
                    <span>Front</span>
                </button>
                <button type="button" class="control-btn camera-btn" data-view="right" title="Right View">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none">
                        <path d="M19 12L5 12M19 12L15 8M19 12L15 16" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                    </svg>
                    <span>Right</span>
                </button>
                <button type="button" class="control-btn camera-btn" data-view="left" title="Left View">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none">
                        <path d="M5 12L19 12M5 12L9 8M5 12L9 16" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                    </svg>
                    <span>Left</span>
                </button>
                <button type="button" class="control-btn camera-btn" data-view="bottom" title="Bottom View">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none">
                        <path d="M12 19L12 5M12 19L8 15M12 19L16 15" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                    </svg>
                    <span>Bottom</span>
                </button>
                <button type="button" class="control-btn camera-btn" data-view="reset" title="Reset Camera">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none">
                        <path d="M12 4V1L8 5L12 9V6C15.31 6 18 8.69 18 12C18 13.01 17.75 13.97 17.3 14.8L18.76 16.26C19.54 15.03 20 13.57 20 12C20 7.58 16.42 4 12 4ZM12 18C8.69 18 6 15.31 6 12C6 10.99 6.25 10.03 6.7 9.2L5.24 7.74C4.46 8.97 4 10.43 4 12C4 16.42 7.58 20 12 20V23L16 19L12 15V18Z" fill="currentColor"/>
                    </svg>
                    <span>Reset</span>
                </button>
            </div>
        </div>

        <div class="control-divider"></div>

        <!-- Tools -->
        <div class="control-section tools-section">
            <div class="control-label">Tools</div>
            <div class="tools-grid">
                <button type="button" class="control-btn active" id="toggleGridBtnMain" title="Toggle Grid">
                    <svg width="16" height="16" viewBox="0 0 18 18" fill="none">
                        <rect x="2" y="2" width="14" height="14" stroke="currentColor" stroke-width="1.5"/>
                        <line x1="9" y1="2" x2="9" y2="16" stroke="currentColor" stroke-width="1.5"/>
                        <line x1="2" y1="9" x2="16" y2="9" stroke="currentColor" stroke-width="1.5"/>
                    </svg>
                    <span>Grid</span>
                </button>
                <button type="button" class="control-btn" id="measureToolBtnMain" title="Measure distances on model">
                    <svg width="16" height="16" viewBox="0 0 18 18" fill="none">
                        <line x1="3" y1="15" x2="15" y2="3" stroke="currentColor" stroke-width="1.5"/>
                        <line x1="3" y1="12" x2="3" y2="15" stroke="currentColor" stroke-width="1.5"/>
                        <line x1="6" y1="15" x2="3" y2="15" stroke="currentColor" stroke-width="1.5"/>
                        <line x1="15" y1="6" x2="15" y2="3" stroke="currentColor" stroke-width="1.5"/>
                        <line x1="12" y1="3" x2="15" y2="3" stroke="currentColor" stroke-width="1.5"/>
                    </svg>
                    <span>Measure</span>
                </button>
                <button type="button" class="control-btn" id="repairAndFillBtnMain" title="Repair mesh and fill holes">
                    <svg width="16" height="16" viewBox="0 0 18 18" fill="none">
                        <path d="M9 2L3 8L9 14L15 8L9 2Z" stroke="currentColor" stroke-width="1.5"/>
                        <circle cx="12" cy="12" r="4" fill="currentColor" opacity="0.3"/>
                    </svg>
                    <span>Repair & Fill</span>
                </button>
                <button type="button" class="control-btn" id="autoRotateBtnMain" title="Auto-rotate model (upload a file first)" style="opacity: 0.5; cursor: not-allowed;" disabled>
                    <svg width="16" height="16" viewBox="0 0 18 18" fill="none">
                        <path d="M15 9C15 12.3137 12.3137 15 9 15C5.68629 15 3 12.3137 3 9C3 5.68629 5.68629 3 9 3C11.0605 3 12.8792 4.01099 14 5.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                        <path d="M15 3V7H11" stroke="currentColor" stroke-width="1.5"/>
                    </svg>
                    <span>Rotate</span>
                </button>
            </div>
        </div>

        <div class="control-divider"></div>

        <!-- Action Buttons -->
        <div class="control-section actions-section">
            <button type="button" class="control-btn share-btn" id="shareBtnMain" title="Share this 3D model" style="margin-right: 12px;">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none">
                    <circle cx="18" cy="5" r="3" stroke="currentColor" stroke-width="2"/>
                    <circle cx="6" cy="12" r="3" stroke="currentColor" stroke-width="2"/>
                    <circle cx="18" cy="19" r="3" stroke="currentColor" stroke-width="2"/>
                    <path d="M8.59 13.51L15.42 17.49M15.41 6.51L8.59 10.49" stroke="currentColor" stroke-width="2"/>
                </svg>
                <span>Share</span>
            </button>
            
            <button type="button" class="control-btn save-btn" id="saveCalculationsBtnMain" title="Save and calculate pricing">
                <svg width="18" height="18" viewBox="0 0 18 18" fill="none">
                    <path d="M15 16H3C2.44772 16 2 15.5523 2 15V3C2 2.44772 2.44772 2 3 2H12L16 6V15C16 15.5523 15.5523 16 15 16Z" stroke="currentColor" stroke-width="1.5"/>
                    <path d="M5 10H13V16H5V10Z" stroke="currentColor" stroke-width="1.5"/>
                </svg>
                <span>Save & Calculate</span>
            </button>
        </div>
    </div>
</div>

<script>
    // ============================================
    // CONTROL BAR FUNCTIONALITY - THREE.JS
    // ============================================
    
    // Global state for measurement tool (needs to be accessible by event handlers)
    let measurementMode = false;
    let measurementPoints = [];
    let measurementMarkers = [];
    let measurementLine = null;
    
    // Wait for everything to load including quote-viewer scripts
    window.addEventListener('load', function() {
        console.log('🎯 Initializing control bar for Three.js...');
        console.log('🔍 Script loaded successfully at:', new Date().toLocaleTimeString());
        
        // Small delay to ensure quote-viewer is fully initialized
        setTimeout(initControls, 500);
    });

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
            measureToolBtn.addEventListener('click', function() {
                measurementMode = !measurementMode;
                this.classList.toggle('active', measurementMode);
                measurementPanel.classList.toggle('active', measurementMode);
                
                const viewer = window.viewerGeneral || window.viewerMedical;
                
                if (measurementMode) {
                    console.log('📏 Measurement mode activated - Controls disabled');
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
                document.getElementById('measurementResult').style.display = 'none';
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
        function setupMeasurementClickHandler() {
            const viewer = window.viewerGeneral || window.viewerMedical;
            if (!viewer || !viewer.renderer) {
                console.warn('⚠️ Viewer or renderer not available for measurement setup');
                return;
            }

            const canvas = viewer.renderer.domElement;
            console.log('✅ Measurement click handler setup complete on canvas');
            
            canvas.addEventListener('click', function(event) {
                if (!measurementMode) {
                    console.log('⏸️ Click ignored - measurement mode not active');
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

                    // Update UI
                    const coordStr = `(${point.x.toFixed(2)}, ${point.y.toFixed(2)}, ${point.z.toFixed(2)})`;
                    if (measurementPoints.length === 1) {
                        document.getElementById('point1Coords').textContent = coordStr;
                    } else if (measurementPoints.length === 2) {
                        document.getElementById('point2Coords').textContent = coordStr;
                        
                        // Calculate distance
                        const distance = measurementPoints[0].distanceTo(measurementPoints[1]);
                        document.getElementById('distanceValue').textContent = distance.toFixed(2) + ' mm';
                        document.getElementById('measurementResult').style.display = 'block';

                        // Draw line between points
                        const lineGeometry = new THREE.BufferGeometry().setFromPoints(measurementPoints);
                        const lineMaterial = new THREE.LineBasicMaterial({ 
                            color: 0xff6b35, 
                            linewidth: 2 
                        });
                        measurementLine = new THREE.Line(lineGeometry, lineMaterial);
                        viewer.scene.add(measurementLine);

                        console.log('📏 Distance:', distance.toFixed(2), 'mm');

                        // Reset for next measurement
                        setTimeout(() => {
                            measurementPoints = [];
                            document.getElementById('point1Coords').textContent = '-';
                            document.getElementById('point2Coords').textContent = '-';
                        }, 100);
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

                console.log('🎯 Raycasting against', meshes.length, 'meshes');

                const intersects = raycaster.intersectObjects(meshes, true);

                const currentReadingEl = document.getElementById('currentReading');
                const readingLabelEl = document.getElementById('readingLabel');

                if (intersects.length > 0) {
                    const point = intersects[0].point;
                    
                    console.log('📍 Hover intersect at:', {
                        x: point.x.toFixed(2),
                        y: point.y.toFixed(2),
                        z: point.z.toFixed(2),
                        distance: intersects[0].distance.toFixed(2),
                        measurementPoints: measurementPoints.length
                    });
                    
                    // If we have one point already, show distance to hover point
                    if (measurementPoints.length === 1) {
                        const distance = measurementPoints[0].distanceTo(point);
                        console.log('📏 Distance to hover point:', distance.toFixed(2), 'mm');
                        if (currentReadingEl) currentReadingEl.textContent = distance.toFixed(2) + ' mm';
                        if (readingLabelEl) readingLabelEl.textContent = 'Wall Thickness';
                    } else {
                        // Show hover instructions
                        if (currentReadingEl) currentReadingEl.textContent = 'Click to start';
                        if (readingLabelEl) readingLabelEl.textContent = 'Hover over model';
                    }
                } else {
                    console.log('❌ No intersection found');
                    if (currentReadingEl) currentReadingEl.textContent = 'Hover over model';
                    if (readingLabelEl) readingLabelEl.textContent = 'to start measuring';
                }
            });
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

        // Repair & Fill - Combined button that does both operations
        const repairAndFillBtn = document.getElementById('repairAndFillBtnMain');
        if (repairAndFillBtn) {
            repairAndFillBtn.addEventListener('click', function() {
                if (modelRepaired && holesFilled) {
                    console.log('✅ Model already repaired and filled');
                    return;
                }
                
                const viewer = window.viewerGeneral || window.viewerMedical;
                if (!viewer) {
                    console.log('⚠️ No viewer available');
                    return;
                }
                
                console.log('🔧 Repairing and filling holes...');
                this.classList.add('active');
                const origHTML = this.innerHTML;
                this.innerHTML = '<svg width="16" height="16" viewBox="0 0 18 18" fill="none"><circle cx="9" cy="9" r="7" stroke="currentColor" stroke-width="1.5" class="spinning"/></svg><span>Processing...</span>';

                setTimeout(() => {
                    // First repair the model
                    console.log('🔧 Step 1: Repairing model...');
                    const repairSuccess = viewer.repairModel();
                    
                    if (repairSuccess) {
                        modelRepaired = true;
                        console.log('✅ Repair successful!');
                        
                        // Then fill holes
                        setTimeout(() => {
                            console.log('🔧 Step 2: Filling holes...');
                            const fillSuccess = viewer.fillHoles();
                            
                            if (fillSuccess) {
                                holesFilled = true;
                                this.innerHTML = '<svg width="16" height="16" viewBox="0 0 18 18" fill="none"><path d="M5 9L8 12L13 6" stroke="currentColor" stroke-width="2"/></svg><span>Completed ✓</span>';
                                console.log('✅ Model repaired and filled successfully!');
                                
                                // Update model dimensions with final accurate volume
                                setTimeout(updateModelDimensions, 500);
                            } else {
                                this.innerHTML = '<svg width="16" height="16" viewBox="0 0 18 18" fill="none"><path d="M5 9L8 12L13 6" stroke="currentColor" stroke-width="2"/></svg><span>Repaired ✓</span>';
                                console.log('⚠️ Fill holes failed, but repair successful');
                                setTimeout(updateModelDimensions, 500);
                            }
                        }, 300);
                    } else {
                        this.innerHTML = origHTML;
                        this.classList.remove('active');
                        console.log('❌ Repair failed');
                    }
                }, 300);
            });
        }

        // Save button
        const saveBtn = document.getElementById('saveCalculationsBtnMain');
        if (saveBtn) {
            saveBtn.addEventListener('click', function() {
                console.log('💾 Saving...');
                const original = this.innerHTML;
                this.innerHTML = '<svg width="18" height="18" viewBox="0 0 18 18" fill="none"><path d="M5 9L8 12L13 6" stroke="currentColor" stroke-width="2"/></svg><span>Saved! ✓</span>';
                this.style.pointerEvents = 'none';

                setTimeout(() => {
                    this.innerHTML = original;
                    this.style.pointerEvents = '';
                }, 2000);

                // Trigger pricing update
                if (window.fileManagerGeneral) {
                    window.fileManagerGeneral.updateQuote();
                }
            });
        }

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
                console.log('✅ Auto-rotation enabled');
            }
        }

        function stopAutoRotation() {
            console.log('🛑 Stopping auto-rotation');
            const viewer = window.viewerGeneral || window.viewerMedical;
            if (!viewer || !viewer.controls) return;

            if (viewer.controls.autoRotate !== undefined) {
                viewer.controls.autoRotate = false;
                console.log('✅ Auto-rotation disabled');
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
        shareBtnMain.addEventListener('click', async function() {
            try {
                // Check if file is uploaded (either in storage or viewer)
                const viewer = window.viewerGeneral || window.viewerMedical;
                const hasUploadedFile = viewer && viewer.uploadedFiles && viewer.uploadedFiles.length > 0;
                let fileId = window.fileStorageManager?.currentFileId;
                
                console.log('🔍 Share button clicked - Current file ID:', fileId);
                console.log('   Has uploaded file:', hasUploadedFile);
                
                if (!fileId && !hasUploadedFile) {
                    showNotification('⚠️ Please upload a 3D model first', 'warning');
                    return;
                }

                // If file is uploaded but not saved yet, trigger save and wait
                if (hasUploadedFile && !fileId) {
                    showNotification('💾 Preparing file for sharing...', 'info');
                    
                    // Get the uploaded file from viewer
                    const uploadedFile = viewer.uploadedFiles[0];
                    if (!uploadedFile || !uploadedFile.file) {
                        showNotification('❌ Failed to access file data', 'error');
                        return;
                    }

                    try {
                        // Convert File to ArrayBuffer
                        const arrayBuffer = await uploadedFile.file.arrayBuffer();
                        
                        // Save file to IndexedDB
                        fileId = await window.fileStorageManager.saveFile(
                            arrayBuffer,
                            uploadedFile.file.name,
                            uploadedFile.geometry,
                            uploadedFile.mesh
                        );

                        console.log('✅ File saved with ID:', fileId);
                    } catch (saveError) {
                        console.error('❌ Failed to save file:', saveError);
                        showNotification('❌ Failed to save file: ' + saveError.message, 'error');
                        return;
                    }
                }

                // Final validation before opening modal
                if (!fileId || fileId === 'null' || fileId === 'undefined' || !fileId.startsWith('file_')) {
                    showNotification('❌ Invalid file ID. Please try uploading the file again.', 'error');
                    console.error('❌ Invalid file ID:', fileId);
                    return;
                }

                // Verify file exists in storage
                const fileRecord = await window.fileStorageManager.loadFile(fileId);
                if (!fileRecord) {
                    showNotification('❌ File not found in storage. Please upload again.', 'error');
                    return;
                }

                // Save current camera state before sharing
                await saveCameraState();
                
                // Open share modal with validated file ID
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
            await viewer.loadFile(file, fileRecord.fileName);
            
            // Wait for model to be fully loaded
            await new Promise(resolve => setTimeout(resolve, 1000));
            
            // Restore camera state
            if (fileRecord.edits && fileRecord.edits.camera) {
                console.log('📷 Restoring camera state...');
                setTimeout(() => {
                    restoreCameraState(fileRecord.edits.camera);
                }, 500);
            }

            // Set current file ID
            window.fileStorageManager.currentFileId = fileId;

            showNotification('✅ Shared model loaded successfully!', 'success');
            console.log('✅ Shared file loaded successfully');
            console.log('   Current file ID set to:', fileId);
            
            // Restart auto-save
            startAutoSave(fileId);
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
            viewer.render();
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
            await viewer.loadFile(file, lastFile.fileName);
            
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
        toast.textContent = message;
        
        const colors = {
            success: '#27ae60',
            error: '#e74c3c',
            warning: '#f39c12',
            info: '#3498db'
        };

        toast.style.cssText = `
            position: fixed;
            bottom: 32px;
            right: 32px;
            padding: 16px 24px;
            background: ${colors[type] || colors.info};
            color: white;
            border-radius: 10px;
            font-size: 15px;
            font-weight: 500;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
            z-index: 9999;
            animation: notifSlideIn 0.3s ease;
        `;

        document.body.appendChild(toast);

        setTimeout(() => {
            toast.style.animation = 'notifSlideOut 0.3s ease';
            setTimeout(() => toast.remove(), 300);
        }, 3000);
    }

    // Add notification animations
    const notifStyles = document.createElement('style');
    notifStyles.textContent = `
        @keyframes notifSlideIn {
            from {
                transform: translateX(400px);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }
        @keyframes notifSlideOut {
            from {
                transform: translateX(0);
                opacity: 1;
            }
            to {
                transform: translateX(400px);
                opacity: 0;
            }
        }
    `;
    document.head.appendChild(notifStyles);
</script>
@endsection
