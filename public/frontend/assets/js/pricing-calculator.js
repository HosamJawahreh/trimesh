/**
 * ========================================
 * ACCURATE PRICING CALCULATOR
 * Based on volume, technology, and material
 * ========================================
 */

console.log('💰 Loading Pricing Calculator...');

window.PricingCalculator = {

    // Comprehensive pricing matrix (USD per cm³)
    pricingMatrix: {
        fdm: {
            pla: 0.50,
            abs: 0.60,
            petg: 0.70,
            tpu: 0.85,
            nylon: 1.20,
            'carbon-fiber': 2.50
        },
        sla: {
            resin: 2.50,
            'tough-resin': 3.00,
            'flexible-resin': 3.50,
            'medical-resin': 4.00,
            'dental-resin': 4.50,
            'castable-resin': 5.00
        },
        sls: {
            nylon: 3.50,
            'nylon-glass': 4.50,
            tpu: 4.00
        },
        dmls: {
            steel: 12.00,
            'stainless-steel': 13.00,
            titanium: 15.00,
            aluminum: 10.00,
            'inconel': 18.00
        },
        mjf: {
            nylon: 3.00,
            'nylon-glass': 4.00
        },
        polyjet: {
            'rigid': 4.50,
            'flexible': 5.00,
            'transparent': 5.50,
            'multi-color': 6.00
        }
    },

    // Print speed factors (hours per cm³)
    printSpeedFactors: {
        fdm: 0.5,
        sla: 0.3,
        sls: 0.4,
        dmls: 1.0,
        mjf: 0.35,
        polyjet: 0.4
    },

    /**
     * Get price per cm³ for specific technology and material
     */
    getPricePerCm3(technology, material) {
        const tech = (technology || 'fdm').toLowerCase();
        const mat = (material || 'pla').toLowerCase();

        console.log(`💰 Looking up price: ${tech} / ${mat}`);

        if (this.pricingMatrix[tech] && this.pricingMatrix[tech][mat]) {
            const price = this.pricingMatrix[tech][mat];
            console.log(`   Found: $${price}/cm³`);
            return price;
        }

        // Fallback to default
        console.warn(`   ⚠️ Price not found, using default $1.00/cm³`);
        return 1.00;
    },

    /**
     * Calculate total price
     */
    calculatePrice(volumeCM3, technology, material) {
        const pricePerCm3 = this.getPricePerCm3(technology, material);
        const totalPrice = volumeCM3 * pricePerCm3;

        console.log(`💰 Price calculation:`);
        console.log(`   Volume: ${volumeCM3.toFixed(2)} cm³`);
        console.log(`   Technology: ${technology}`);
        console.log(`   Material: ${material}`);
        console.log(`   Rate: $${pricePerCm3.toFixed(2)}/cm³`);
        console.log(`   Total: $${totalPrice.toFixed(2)}`);

        return {
            volumeCM3: volumeCM3,
            pricePerCm3: pricePerCm3,
            totalPrice: totalPrice,
            technology: technology,
            material: material
        };
    },

    /**
     * Estimate print time
     */
    estimatePrintTime(volumeCM3, technology) {
        const tech = (technology || 'fdm').toLowerCase();
        const factor = this.printSpeedFactors[tech] || 0.5;
        const hours = volumeCM3 * factor;

        if (hours < 1) {
            const minutes = Math.ceil(hours * 60);
            return `${minutes}min`;
        } else if (hours < 24) {
            return `${hours.toFixed(1)}h`;
        } else {
            const days = Math.floor(hours / 24);
            const remainingHours = Math.round(hours % 24);
            return `${days}d ${remainingHours}h`;
        }
    },

    /**
     * Get all available technologies
     */
    getTechnologies() {
        return Object.keys(this.pricingMatrix);
    },

    /**
     * Get materials for a specific technology
     */
    getMaterialsForTechnology(technology) {
        const tech = (technology || 'fdm').toLowerCase();
        if (this.pricingMatrix[tech]) {
            return Object.keys(this.pricingMatrix[tech]);
        }
        return [];
    }
};

console.log('✅ Pricing Calculator loaded');
console.log(`   Technologies: ${window.PricingCalculator.getTechnologies().join(', ')}`);
