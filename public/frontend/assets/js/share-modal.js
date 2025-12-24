/**
 * ShareModal - Beautiful modal for sharing 3D viewer links
 * - Copy link to clipboard
 * - Generate QR code
 * - Show expiry countdown
 * - Shapeways-inspired design
 */

class ShareModal {
    constructor() {
        this.isOpen = false;
        this.qrCode = null;
        this.init();
    }

    init() {
        // Create modal HTML
        const modalHTML = `
            <div id="shareModal" class="share-modal" style="display: none;">
                <div class="share-modal-overlay"></div>
                <div class="share-modal-content">
                    <div class="share-modal-header">
                        <h2>🔗 Share Your 3D Model</h2>
                        <button class="share-modal-close" onclick="shareModal.close()">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <line x1="18" y1="6" x2="6" y2="18"></line>
                                <line x1="6" y1="6" x2="18" y2="18"></line>
                            </svg>
                        </button>
                    </div>

                    <div class="share-modal-body">
                        <p class="share-description">
                            Anyone with this link can view your 3D model and all edits for 72 hours.
                        </p>

                        <div class="share-link-container">
                            <input
                                type="text"
                                id="shareLinkInput"
                                class="share-link-input"
                                readonly
                                placeholder="Generating link..."
                            />
                            <button class="share-copy-btn" onclick="shareModal.copyLink()">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect>
                                    <path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path>
                                </svg>
                                Copy Link
                            </button>
                        </div>

                        <div class="share-qr-section">
                            <div class="share-qr-title">Or scan QR code:</div>
                            <div id="shareQRCode" class="share-qr-code"></div>
                            <button class="share-download-qr" onclick="shareModal.downloadQR()">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                                    <polyline points="7 10 12 15 17 10"></polyline>
                                    <line x1="12" y1="15" x2="12" y2="3"></line>
                                </svg>
                                Download QR Code
                            </button>
                        </div>

                        <div class="share-expiry">
                              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="12" cy="12" r="10"></circle>
                                <polyline points="12 6 12 12 16 14"></polyline>
                            </svg>
                            <span id="shareExpiryTime">72 hours remaining</span>
                        </div>

                    </div>
                </div>
            </div>
        `;

        // Add modal to page
        document.body.insertAdjacentHTML('beforeend', modalHTML);

        // Add styles
        this.addStyles();
    }

    addStyles() {
        const styles = `
            <style>
                .share-modal {
                    position: fixed;
                    top: 0;
                    left: 0;
                    width: 100%;
                    height: 100%;
                    z-index: 10000;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    animation: shareModalFadeIn 0.3s ease;
                }

                @keyframes shareModalFadeIn {
                    from { opacity: 0; }
                    to { opacity: 1; }
                }

                .share-modal-overlay {
                    position: absolute;
                    top: 0;
                    left: 0;
                    width: 100%;
                    height: 100%;
                    background: rgba(0, 0, 0, 0.7);
                    backdrop-filter: blur(4px);
                }

                .share-modal-content {
                    position: relative;
                    background: white;
                    border-radius: 16px;
                    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
                    width: 90%;
                    max-width: 600px;
                    max-height: 90vh;
                    overflow-y: auto;
                    animation: shareModalSlideUp 0.4s cubic-bezier(0.16, 1, 0.3, 1);
                }

                @keyframes shareModalSlideUp {
                    from {
                        transform: translateY(50px);
                        opacity: 0;
                    }
                    to {
                        transform: translateY(0);
                        opacity: 1;
                    }
                }

                .share-modal-header {
                    display: flex;
                    justify-content: space-between;
                    align-items: center;
                    padding: 24px 32px;
                    border-bottom: 1px solid #e0e0e0;
                }

                .share-modal-header h2 {
                    margin: 0;
                    font-size: 24px;
                    font-weight: 600;
                    color: #2c3e50;
                }

                .share-modal-close {
                    background: none;
                    border: none;
                    cursor: pointer;
                    padding: 8px;
                    border-radius: 8px;
                    color: #7f8c8d;
                    transition: all 0.2s;
                }

                .share-modal-close:hover {
                    background: #f0f0f0;
                    color: #2c3e50;
                }

                .share-modal-body {
                    padding: 32px;
                }

                .share-description {
                    color: #7f8c8d;
                    font-size: 15px;
                    margin: 0 0 24px 0;
                    line-height: 1.6;
                }

                .share-link-container {
                    display: flex;
                    gap: 12px;
                    margin-bottom: 32px;
                }

                .share-link-input {
                    flex: 1;
                    padding: 14px 16px;
                    border: 2px solid #e0e0e0;
                    border-radius: 10px;
                    font-size: 14px;
                    font-family: 'Courier New', monospace;
                    color: #2c3e50;
                    background: #f8f9fa;
                    transition: all 0.2s;
                }

                .share-link-input:focus {
                    outline: none;
                    border-color: #3498db;
                    background: white;
                }

                .share-copy-btn {
                    padding: 14px 24px;
                    background: linear-gradient(135deg, #3498db 0%, #2980b9 100%);
                    color: white;
                    border: none;
                    border-radius: 10px;
                    font-size: 15px;
                    font-weight: 600;
                    cursor: pointer;
                    display: flex;
                    align-items: center;
                    gap: 8px;
                    transition: all 0.3s;
                    box-shadow: 0 4px 12px rgba(52, 152, 219, 0.3);
                }

                .share-copy-btn:hover {
                    transform: translateY(-2px);
                    box-shadow: 0 6px 20px rgba(52, 152, 219, 0.4);
                }

                .share-copy-btn:active {
                    transform: translateY(0);
                }

                .share-copy-btn.copied {
                    background: linear-gradient(135deg, #27ae60 0%, #229954 100%);
                }

                .share-qr-section {
                    text-align: center;
                    padding: 24px;
                    background: #f8f9fa;
                    border-radius: 12px;
                    margin-bottom: 24px;
                }

                .share-qr-title {
                    font-size: 14px;
                    color: #7f8c8d;
                    margin-bottom: 16px;
                    font-weight: 500;
                }

                .share-qr-code {
                    display: inline-block;
                    padding: 16px;
                    background: white;
                    border-radius: 12px;
                    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
                    margin-bottom: 16px;
                }

                .share-qr-code canvas {
                    display: block;
                }

                .share-download-qr {
                    padding: 10px 20px;
                    background: white;
                    color: #3498db;
                    border: 2px solid #3498db;
                    border-radius: 8px;
                    font-size: 14px;
                    font-weight: 500;
                    cursor: pointer;
                    display: inline-flex;
                    align-items: center;
                    gap: 8px;
                    transition: all 0.2s;
                }

                .share-download-qr:hover {
                    background: #3498db;
                    color: white;
                }

                .share-expiry {
                    display: flex;
                    align-items: center;
                    gap: 10px;
                    padding: 16px;
                    background: #fff3cd;
                    border: 1px solid #ffc107;
                    border-radius: 10px;
                    color: #856404;
                    font-size: 14px;
                    font-weight: 500;
                    margin-bottom: 24px;
                }

                .share-features {
                    display: grid;
                    gap: 12px;
                }

                .share-feature {
                    display: flex;
                    align-items: center;
                    gap: 12px;
                    color: #2c3e50;
                    font-size: 14px;
                }

                .share-feature svg {
                    color: #27ae60;
                    flex-shrink: 0;
                }

            </style>
        `;
        document.head.insertAdjacentHTML('beforeend', styles);
    }

    async open(fileId) {
        const modal = document.getElementById('shareModal');
        modal.style.display = 'flex';
        this.isOpen = true;

        // Get shareable link
        const link = window.fileStorageManager.getShareableLink(fileId);
        document.getElementById('shareLinkInput').value = link;

        // Generate QR code
        await this.generateQR(link);

        // Update expiry time
        this.updateExpiryTime(fileId);

        // Prevent body scroll
        document.body.style.overflow = 'hidden';
    }

    close() {
        const modal = document.getElementById('shareModal');
        modal.style.display = 'none';
        this.isOpen = false;

        // Re-enable body scroll
        document.body.style.overflow = '';
    }

    async copyLink() {
        const input = document.getElementById('shareLinkInput');
        const btn = document.querySelector('.share-copy-btn');

        try {
            await navigator.clipboard.writeText(input.value);

            // Visual feedback
            btn.classList.add('copied');
            btn.innerHTML = `
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <polyline points="20 6 9 17 4 12"></polyline>
                </svg>
                Copied!
            `;

            // Show toast notification
            this.showToast('✅ Link copied to clipboard!');

            // Reset after 2 seconds
            setTimeout(() => {
                btn.classList.remove('copied');
                btn.innerHTML = `
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect>
                        <path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path>
                    </svg>
                    Copy Link
                `;
            }, 2000);
        } catch (error) {
            console.error('Failed to copy:', error);
            this.showToast('❌ Failed to copy link', 'error');
        }
    }

    async generateQR(link) {
        const qrContainer = document.getElementById('shareQRCode');
        qrContainer.innerHTML = '';

        // Use QRCode.js library
        if (typeof QRCode !== 'undefined') {
            this.qrCode = new QRCode(qrContainer, {
                text: link,
                width: 200,
                height: 200,
                colorDark: "#000000",
                colorLight: "#ffffff",
                correctLevel: QRCode.CorrectLevel.H
            });
        } else {
            qrContainer.innerHTML = '<p style="color: #7f8c8d;">QR Code library not loaded</p>';
        }
    }

    downloadQR() {
        const canvas = document.querySelector('#shareQRCode canvas');
        if (!canvas) return;

        canvas.toBlob((blob) => {
            const url = URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = 'trimesh-qr-code.png';
            a.click();
            URL.revokeObjectURL(url);

            this.showToast('✅ QR code downloaded!');
        });
    }

    async updateExpiryTime(fileId) {
        const fileRecord = await window.fileStorageManager.loadFile(fileId);
        if (fileRecord) {
            const timeRemaining = window.fileStorageManager.getTimeRemaining(fileRecord);
            document.getElementById('shareExpiryTime').textContent = timeRemaining;
        }
    }

    showToast(message, type = 'success') {
        const toast = document.createElement('div');
        toast.className = 'share-toast';

        // Create toast with icon
        const icon = type === 'error' ? '⚠️' : '✓';
        const bgGradient = type === 'error'
            ? 'linear-gradient(135deg, #f093fb 0%, #f5576c 100%)'
            : 'linear-gradient(135deg, #11998e 0%, #38ef7d 100%)';

        toast.innerHTML = `
            <div style="display: flex; align-items: center; gap: 12px;">
                <span style="font-size: 20px; display: flex; align-items: center; justify-content: center; width: 32px; height: 32px; background: rgba(255,255,255,0.2); border-radius: 50%;">${icon}</span>
                <span>${message.replace(/^[✅❌]\s*/, '')}</span>
            </div>
        `;

        toast.style.cssText = `
            position: fixed;
            top: 20px;
            left: 50%;
            transform: translateX(-50%) translateY(-100%);
            padding: 16px 28px;
            background: ${bgGradient};
            color: white;
            border-radius: 12px;
            font-size: 15px;
            font-weight: 500;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.25);
            z-index: 10001;
            animation: toastSlideInTop 0.4s cubic-bezier(0.34, 1.56, 0.64, 1) forwards;
            backdrop-filter: blur(10px);
        `;

        document.body.appendChild(toast);

        setTimeout(() => {
            toast.style.animation = 'toastSlideOutTop 0.3s ease forwards';
            setTimeout(() => toast.remove(), 300);
        }, 3000);
    }
}

// Create global instance
window.shareModal = new ShareModal();

// Add toast animations
const toastStyles = document.createElement('style');
toastStyles.textContent = `
    @keyframes toastSlideInTop {
        from {
            transform: translateX(-50%) translateY(-100%);
            opacity: 0;
        }
        to {
            transform: translateX(-50%) translateY(0);
            opacity: 1;
        }
    }

    @keyframes toastSlideOutTop {
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
document.head.appendChild(toastStyles);
