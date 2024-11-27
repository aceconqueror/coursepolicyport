<script>
    document.addEventListener("DOMContentLoaded", function () {
        const profilePic = document.querySelector('.profile-pic');
        const dropdownContent = document.querySelector('.dropdown-content');

        // Toggle dropdown on profile picture click
        profilePic.addEventListener('click', function (e) {
            e.stopPropagation(); // Prevent click event from propagating to the window
            dropdownContent.style.display = (dropdownContent.style.display === 'block') ? 'none' : 'block';
        });

        // Close dropdown when clicking outside
        window.addEventListener('click', function (e) {
            if (!profilePic.contains(e.target) && !dropdownContent.contains(e.target)) {
                dropdownContent.style.display = 'none';
            }
        });
    });
</script>
