 <!-- jQuery (must be included first) -->
 <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>

 <!-- Summernote CSS -->
 <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.20/dist/summernote.min.css" rel="stylesheet">
 <!-- Summernote JS -->
 <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.20/dist/summernote.min.js"></script>

 {{-- SLICK CAROUSEL --}}
 <script type="text/javascript" src="//cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.min.js"></script>

 {{-- BOOTSTART ICONS --}}
 <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

 <script>
     window.onload = function() {
         const notyf = new Notyf();
         // notyf.success('Validation Error');
         // notyf.error('Something went Wrong, Please try again');
         $('textarea, .textarea, #textarea').summernote({
             placeholder: 'Contents',
             tabsize: 10,
             height: 300,
             toolbar: [
                 ['style', ['style']],
                 ['font', ['bold', 'underline', 'clear']],
                 ['color', ['color']],
                 ['para', ['ul', 'ol', 'paragraph']],
                 ['table', ['table']],
                 ['insert', ['link', 'picture', 'video']],
                 ['view', ['fullscreen', 'codeview', 'help']]
             ]
         });



     }
 </script>
 @if (env('WEBSITE_ENABLE_LOADING_SCREEN'))
     <script>
         /* LOADING SCREEN START */
         // Show the loading screen initially
         //  document.getElementById('loading-screen').style.display = 'block';

         // Hide the loading screen after 5 seconds
         setTimeout(function() {
             document.getElementById('loading-screen').style.display = 'none';
         }, 1000); // 5000 milliseconds = 5 seconds

         // Show the main content
         document.getElementById('main-content').style.display = 'block';

         /* LOADING SCREEN END */
     </script>
 @endif
 @if (env('WEBSITE_ENABLE_DARK_LIGHT_FEATURE'))
     <script>
         // Function to set a cookie
         function setCookie(name, value, days) {
             const date = new Date();
             date.setTime(date.getTime() + days * 24 * 60 * 60 * 1000); // Cookie expiration in days
             document.cookie = `${name}=${value}; expires=${date.toUTCString()}; path=/`;
         }

         // Function to get a cookie
         function getCookie(name) {
             const cookies = document.cookie.split("; ");
             for (let cookie of cookies) {
                 const [key, value] = cookie.split("=");
                 if (key === name) {
                     return value;
                 }
             }
             return null;
         }

         // Load the theme from the cookie or default to 'light'
         let currentTheme = getCookie("theme") || "light";
         let newTheme = currentTheme === "light" ? "dark" : "light";

         // Apply the theme on page load
         document.documentElement.setAttribute("data-bs-theme", currentTheme);

         // Update the icon on page load
         const themeToggleBtn = document.getElementById("theme-toggle");
         const icon = themeToggleBtn.querySelector("i");
         if (currentTheme === "light") {
             icon.classList.add("bi-moon-stars-fill"); // Moon icon for dark mode
             icon.classList.remove("bi-sun-fill");
         } else {
             icon.classList.add("bi-sun-fill"); // Sun icon for light mode
             icon.classList.remove("bi-moon-stars-fill");
         }

         // Add click event listener to the theme toggle button
         themeToggleBtn.addEventListener("click", function() {
             // Toggle the theme
             newTheme = currentTheme === "light" ? "dark" : "light";
             currentTheme = newTheme;

             // Apply the new theme
             document.documentElement.setAttribute("data-bs-theme", newTheme);

             // Save the theme to a cookie
             setCookie("theme", newTheme, 30); // Save for 30 days

             // Update the icon
             if (newTheme === "light") {
                 icon.classList.remove("bi-sun-fill");
                 icon.classList.add("bi-moon-stars-fill"); // Moon icon for dark mode
             } else {
                 icon.classList.remove("bi-moon-stars-fill");
                 icon.classList.add("bi-sun-fill"); // Sun icon for light mode
             }
         });
     </script>
 @endif
