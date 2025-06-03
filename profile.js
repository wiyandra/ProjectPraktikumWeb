// JavaScript to preview profile image before uploading
function previewImage() {
    const fileInput = document.getElementById('upload-photo');
    const profileImg = document.getElementById('profileImg');
    
    if (fileInput.files && fileInput.files[0]) {
        const reader = new FileReader();
        
        reader.onload = function(e) {
            profileImg.src = e.target.result;
        };
        
        reader.readAsDataURL(fileInput.files[0]);
        
        // Validate file size and type
        const file = fileInput.files[0];
        const fileSize = file.size / 1024 / 1024; // Convert to MB
        const fileType = file.type;
        
        if (fileSize > 5) {
            alert('Ukuran file terlalu besar! Maksimal 5MB.');
            fileInput.value = '';
            profileImg.src = document.getElementById('profileImg').getAttribute('data-default-img');
            return;
        }
        
        if (!fileType.match('image.*')) {
            alert('Hanya file gambar yang diperbolehkan!');
            fileInput.value = '';
            profileImg.src = document.getElementById('profileImg').getAttribute('data-default-img');
            return;
        }
    }
}

// Function to load user profile data
function loadProfileData() {
    fetch('get_profile.php')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.getElementById('profileImg').src = data.profile_image;
                document.getElementById('profileImg').setAttribute('data-default-img', data.profile_image);
                document.querySelector('.text-wrapper-5').textContent = "Hi, " + data.username;
                document.getElementById('nama').value = data.nama;
                document.getElementById('username').value = data.username;
                document.getElementById('email').value = data.email;
                document.getElementById('phone').value = data.no_hp;
            } else {
                console.error('Failed to load profile data:', data.message);
                // Show error message to user
                const statusMessage = document.getElementById('statusMessage');
                if (statusMessage) {
                    statusMessage.textContent = 'Gagal memuat data profil: ' + data.message;
                    statusMessage.className = 'error-message';
                    statusMessage.style.display = 'block';
                }
            }
        })
        .catch(error => {
            console.error('Error loading profile data:', error);
            // Show error message to user
            const statusMessage = document.getElementById('statusMessage');
            if (statusMessage) {
                statusMessage.textContent = 'Error: Gagal terhubung ke server';
                statusMessage.className = 'error-message';
                statusMessage.style.display = 'block';
            }
        });
}

// Handle form submission
function handleProfileSubmit(event) {
    const form = document.querySelector('form');
    const formData = new FormData(form);
    
    fetch('uploadprofile.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        const statusMessage = document.getElementById('statusMessage');
        if (statusMessage) {
            statusMessage.textContent = data.message;
            statusMessage.className = data.success ? 'success-message' : 'error-message';
            statusMessage.style.display = 'block';
            
            // Hide message after 5 seconds
            setTimeout(() => {
                statusMessage.style.display = 'none';
            }, 5000);
        }
        
        // If success, update the profile image
        if (data.success && data.image_path) {
            document.getElementById('profileImg').src = data.image_path;
        }
    })
    .catch(error => {
        console.error('Error submitting profile:', error);
        // Show error message to user
        const statusMessage = document.getElementById('statusMessage');
        if (statusMessage) {
            statusMessage.textContent = 'Error: Gagal terhubung ke server';
            statusMessage.className = 'error-message';
            statusMessage.style.display = 'block';
        }
    });
}

// Load profile data when the page loads
document.addEventListener('DOMContentLoaded', function() {
    loadProfileData();
    
    // Add submit event listener to the form
    const form = document.querySelector('form');
    if (form) {
        form.addEventListener('submit', function(event) {
            event.preventDefault();
            handleProfileSubmit();
        });
    }
});
