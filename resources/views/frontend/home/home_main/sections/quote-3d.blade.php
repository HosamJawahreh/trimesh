<!-- 3D Quote Test Section -->
<section style="background: #f0f0f0; padding: 60px 0;">
    <div class="container">
        <div class="row">
            <div class="col-12 text-center">
                <h2 style="color: #333; margin-bottom: 30px;">3D Quote System - Test</h2>
                <p style="font-size: 18px; color: #666;">This is a test to see if the section is loading</p>

                <!-- Form Type Switcher -->
                <div style="margin: 40px 0;">
                    <button type="button" class="btn btn-primary btn-lg me-3" id="btnGeneral">
                        General 3D Printing
                    </button>
                    <button type="button" class="btn btn-warning btn-lg" id="btnMedical">
                        Medical 3D Printing
                    </button>
                </div>

                <!-- General Form -->
                <div id="generalForm" style="background: white; padding: 40px; border-radius: 10px; margin-top: 30px;">
                    <h3>General 3D Printing Form</h3>
                    <div style="margin-top: 20px;">
                        <input type="file" class="form-control" accept=".stl,.obj,.ply" multiple>
                    </div>
                    <div style="margin-top: 20px; height: 400px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 10px; display: flex; align-items: center; justify-content: center; color: white;">
                        <div>
                            <h4>3D Viewer Placeholder</h4>
                            <p>Upload a file to see it here</p>
                        </div>
                    </div>
                </div>

                <!-- Medical Form -->
                <div id="medicalForm" style="background: white; padding: 40px; border-radius: 10px; margin-top: 30px; display: none;">
                    <h3>Medical 3D Printing Form</h3>
                    <div style="margin-top: 20px;">
                        <input type="file" class="form-control" accept=".stl,.obj,.ply" multiple>
                    </div>
                    <div style="margin-top: 20px; height: 400px; background: linear-gradient(135deg, #ff9800 0%, #ff5722 100%); border-radius: 10px; display: flex; align-items: center; justify-content: center; color: white;">
                        <div>
                            <h4>3D Viewer Placeholder</h4>
                            <p>Upload a medical file to see it here</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const btnGeneral = document.getElementById('btnGeneral');
    const btnMedical = document.getElementById('btnMedical');
    const generalForm = document.getElementById('generalForm');
    const medicalForm = document.getElementById('medicalForm');

    if (btnGeneral && btnMedical) {
        btnGeneral.addEventListener('click', function() {
            generalForm.style.display = 'block';
            medicalForm.style.display = 'none';
        });

        btnMedical.addEventListener('click', function() {
            generalForm.style.display = 'none';
            medicalForm.style.display = 'block';
        });
    }
});
</script>
