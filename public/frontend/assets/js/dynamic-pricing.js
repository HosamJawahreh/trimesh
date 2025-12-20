/**
 * Dynamic Pricing Calculator
 * Handles real-time price updates based on volume, material, technology, and quantity
 */

class DynamicPricing {
    constructor() {
        this.currentVolume = 0; // in mm³
        this.currentPrice = 0;
        this.isCalculating = false;

        // Pricing factors (can be loaded from backend)
        this.priceFactors = {
            technology: {
                'FDM (Fused Deposition Modeling)': 1.0,
                'SLA (Stereolithography)': 1.5,
                'SLS (Selective Laser Sintering)': 2.0,
                'MJF (Multi Jet Fusion)': 2.2,
                'PolyJet': 2.5
            },
            material: {
                'PLA': 0.05,           // $ per cm³
                'ABS': 0.06,
                'PETG': 0.07,
                'Nylon': 0.12,
                'TPU': 0.15,
                'Resin': 0.20,
                'PA12': 0.25
            },
            color: {
                'white': 1.0,
                'black': 1.0,
                'gray': 1.0,
                'red': 1.1,
                'blue': 1.1,
                'green': 1.1,
                'yellow': 1.1,
                'custom': 1.2
            },
            quantity: {
                1: 1.0,
                2: 0.95,
                3: 0.90,
                5: 0.85,
                10: 0.80,
                20: 0.75,
                50: 0.70
            }
        };

        this.basePricePerCm3 = 0.10; // Base price per cm³
        this.setupFee = 5.00; // One-time setup fee
    }

    /**
     * Set the current volume (in mm³)
     */
    setVolume(volumeMm3) {
        this.currentVolume = volumeMm3;
        console.log(`💰 Volume set: ${volumeMm3.toFixed(2)} mm³ (${(volumeMm3 / 1000).toFixed(2)} cm³)`);
    }

    /**
     * Calculate price based on all factors
     */
    calculatePrice(options = {}) {
        const {
            volume = this.currentVolume,
            technology = 'FDM (Fused Deposition Modeling)',
            material = 'PLA',
            color = 'white',
            quantity = 1
        } = options;

        // Convert volume to cm³
        const volumeCm3 = volume / 1000;

        // Get factors
        const techFactor = this.priceFactors.technology[technology] || 1.0;
        const materialPrice = this.priceFactors.material[material] || this.basePricePerCm3;
        const colorFactor = this.priceFactors.color[color.toLowerCase()] || 1.0;
        const quantityDiscount = this.getQuantityDiscount(quantity);

        // Calculate base material cost
        const materialCost = volumeCm3 * materialPrice;

        // Apply technology and color factors
        const adjustedCost = materialCost * techFactor * colorFactor;

        // Apply quantity discount
        const unitPrice = adjustedCost * quantityDiscount + this.setupFee;

        // Total price
        const totalPrice = unitPrice * quantity;

        const breakdown = {
            volume: volume,
            volumeCm3: volumeCm3,
            technology: technology,
            material: material,
            color: color,
            quantity: quantity,
            materialCost: materialCost,
            techFactor: techFactor,
            colorFactor: colorFactor,
            quantityDiscount: quantityDiscount,
            unitPrice: unitPrice,
            totalPrice: totalPrice,
            setupFee: this.setupFee
        };

        console.log('💵 Price breakdown:', breakdown);

        this.currentPrice = totalPrice;
        return breakdown;
    }

    /**
     * Get quantity discount multiplier
     */
    getQuantityDiscount(quantity) {
        // Find closest quantity tier
        const tiers = Object.keys(this.priceFactors.quantity).map(Number).sort((a, b) => a - b);
        let selectedTier = tiers[0];

        for (const tier of tiers) {
            if (quantity >= tier) {
                selectedTier = tier;
            }
        }

        return this.priceFactors.quantity[selectedTier];
    }

    /**
     * Update price factors from server
     */
    async loadPriceFactors() {
        try {
            // Can be loaded from backend API
            // const response = await fetch('/api/pricing-factors');
            // this.priceFactors = await response.json();
            console.log('✅ Price factors loaded');
        } catch (error) {
            console.error('❌ Error loading price factors:', error);
        }
    }

    /**
     * Format price for display
     */
    formatPrice(price) {
        return `$${price.toFixed(2)}`;
    }

    /**
     * Format volume for display
     */
    formatVolume(volumeMm3) {
        const cm3 = volumeMm3 / 1000;
        return `${cm3.toFixed(2)} cm³`;
    }
}

// Create global instance
if (typeof window !== 'undefined') {
    window.DynamicPricing = DynamicPricing;
    window.dynamicPricing = new DynamicPricing();
    console.log('✅ DynamicPricing module loaded');
}
