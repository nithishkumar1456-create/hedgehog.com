/**
 * Hedgehog UI Helper Utilities
 */
const UI = {
    showToast: (message, type = 'error') => {
        const container = document.getElementById('toast-container');
        const toast = document.createElement('div');
        toast.className = `toast ${type}`;
        toast.innerHTML = `<span>${message}</span>`;
        container.appendChild(toast);
        
        setTimeout(() => {
            toast.style.opacity = '0';
            toast.style.transform = 'translateX(50px)';
            setTimeout(() => toast.remove(), 400);
        }, 3500);
    },

    setLoading: (btnId, isLoading, text = 'Processing...') => {
        const btn = document.getElementById(btnId);
        if (!btn) return;
        
        if (isLoading) {
            btn.disabled = true;
            btn._originalHTML = btn.innerHTML;
            btn.innerHTML = `<div class="spinner"></div> <span>${text}</span>`;
        } else {
            btn.disabled = false;
            btn.innerHTML = btn._originalHTML || btn.innerHTML;
        }
    }
};

document.addEventListener('DOMContentLoaded', async () => {
    const token = localStorage.getItem('token');
    if (!token) {
        window.location.href = 'login.html';
        return;
    }

    const profileView = document.getElementById('profileView');
    const profileEdit = document.getElementById('profileEdit');

    const fetchProfile = async () => {
        try {
            const response = await fetch('php/profile.php', {
                method: 'GET',
                headers: { 'Authorization': `Bearer ${token}` }
            });

            const result = await response.json();

            if (response.ok) {
                document.getElementById('display_name').textContent = result.profile.name;
                document.getElementById('display_username').textContent = result.user.username;
                document.getElementById('display_email').textContent = result.user.email;
                document.getElementById('display_age').textContent = result.profile.age;
                document.getElementById('display_dob').textContent = result.profile.dob;
                document.getElementById('display_mobile').textContent = result.profile.mobile;
                document.getElementById('display_id').textContent = `#${result.profile.user_id}`;
                
                // Chili Themed Avatar
                document.getElementById('display_pic').src = `https://api.dicebear.com/7.x/identicon/svg?seed=${result.user.username}&backgroundColor=CD1C18`;

                // Set Edit Fields
                document.getElementById('edit_name').value = result.profile.name;
                document.getElementById('edit_age').value = result.profile.age;
                document.getElementById('edit_mobile').value = result.profile.mobile;
            } else {
                localStorage.removeItem('token');
                window.location.href = 'login.html';
            }
        } catch (error) {
            UI.showToast("Failed to sync vault data.", "error");
        }
    };

    await fetchProfile();

    document.getElementById('editBtn').addEventListener('click', () => {
        profileView.style.display = 'none';
        profileEdit.style.display = 'block';
    });

    document.getElementById('cancelEditBtn').addEventListener('click', () => {
        profileEdit.style.display = 'none';
        profileView.style.display = 'block';
    });

    document.getElementById('logoutBtn').addEventListener('click', () => {
        UI.showToast("Session Terminated.", "info");
        localStorage.removeItem('token');
        setTimeout(() => window.location.href = 'login.html', 800);
    });

    document.getElementById('profileForm').addEventListener('submit', async (e) => {
        e.preventDefault();
        
        const updateData = {
            name: document.getElementById('edit_name').value,
            age: document.getElementById('edit_age').value,
            mobile: document.getElementById('edit_mobile').value
        };

        const newPassword = document.getElementById('edit_password').value;
        if (newPassword) updateData.password = newPassword;

        UI.setLoading('saveBtn', true, 'Pushing Data...');

        try {
            const response = await fetch('php/profile.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': `Bearer ${token}`
                },
                body: JSON.stringify(updateData)
            });

            const result = await response.json();

            if (response.ok) {
                UI.showToast("Vault Updated Successfully.", "success");
                setTimeout(async () => {
                    profileEdit.style.display = 'none';
                    profileView.style.display = 'block';
                    await fetchProfile();
                }, 1000);
            } else {
                UI.showToast(result.error || "Update rejected.", "error");
            }
        } catch (error) {
            UI.showToast("Push failed. Database connection error.", "error");
        } finally {
            UI.setLoading('saveBtn', false);
        }
    });
});