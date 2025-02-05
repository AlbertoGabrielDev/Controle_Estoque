<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Toast com Animações e Status</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes fadeOut {
            from {
                opacity: 1;
                transform: translateY(0);
            }

            to {
                opacity: 0;
                transform: translateY(-20px);
            }
        }

        .fade-in {
            animation: fadeIn 0.5s ease-out forwards;
        }

        .fade-out {
            animation: fadeOut 0.5s ease-out forwards;
        }
    </style>
</head>

<body>

    <div id="toast-default" class="flex items-center w-full max-w-xs p-4 bg-white rounded-lg shadow-sm fixed top-4 right-4 z-50 hidden opacity-0" role="alert">

        <div id="toast-icon" class="inline-flex items-center justify-center shrink-0 w-8 h-8 rounded-lg">

            <svg class="w-4 h-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 18 20">
                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.147 15.085a7.159 7.159 0 0 1-6.189 3.307A6.713 6.713 0 0 1 3.1 15.444c-2.679-4.513.287-8.737.888-9.548A4.373 4.373 0 0 0 5 1.608c1.287.953 6.445 3.218 5.537 10.5 1.5-1.122 2.706-3.01 2.853-6.14 1.433 1.049 3.993 5.395 1.757 9.117Z" />
            </svg>
        </div>

        <div class="ms-3 text-sm font-normal" id="toast-message">Set yourself free.</div>

        <button type="button" class="ms-auto -mx-1.5 -my-1.5 bg-white text-gray-400 hover:text-gray-900 rounded-lg focus:ring-2 focus:ring-gray-300 p-1.5 hover:bg-gray-100 inline-flex items-center justify-center h-8 w-8" data-dismiss-target="#toast-default" aria-label="Close">
            <span class="sr-only">Close</span>
            <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
            </svg>
        </button>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const toastMessage = document.getElementById('toast-message');
            const toast = document.getElementById('toast-default');
            const toastIcon = document.getElementById('toast-icon');

            const successMessage = "{{ session('success') }}";
            const errorMessage = "{{ session('error') }}";
            const warningMessage = "{{ session('warning') }}";
            const infoMessage = "{{ session('info') }}";

            let message = successMessage || errorMessage || warningMessage || infoMessage;
            if (message) {

                toastMessage.textContent = message;

                let iconColor = 'text-blue-500 bg-blue-100';
                let iconPath = 'M15.147 15.085a7.159 7.159 0 0 1-6.189 3.307A6.713 6.713 0 0 1 3.1 15.444c-2.679-4.513.287-8.737.888-9.548A4.373 4.373 0 0 0 5 1.608c1.287.953 6.445 3.218 5.537 10.5 1.5-1.122 2.706-3.01 2.853-6.14 1.433 1.049 3.993 5.395 1.757 9.117Z';

                if (successMessage) {
                    iconColor = 'text-green-500 bg-green-100';
                    iconPath = 'M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5Zm3.707 8.207-4 4a1 1 0 0 1-1.414 0l-2-2a1 1 0 0 1 1.414-1.414L9 10.586l3.293-3.293a1 1 0 0 1 1.414 1.414Z';
                    toast.classList.remove('bg-white');
                    toast.classList.add('bg-green-600');
                } else if (errorMessage) {
                    iconColor = 'text-red-500 bg-red-100';
                    iconPath = 'M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5Zm3.707 11.793a1 1 0 1 1-1.414 1.414L10 11.414l-2.293 2.293a1 1 0 0 1-1.414-1.414L8.586 10 6.293 7.707a1 1 0 0 1 1.414-1.414L10 8.586l2.293-2.293a1 1 0 0 1 1.414 1.414L11.414 10l2.293 2.293Z';
                    toast.classList.remove('bg-white');
                    toast.classList.add('bg-red-600');
                } else if (warningMessage) {
                    iconColor = 'text-yellow-500 bg-yellow-100';
                    iconPath = 'M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM10 15a1 1 0 1 1 0-2 1 1 0 0 1 0 2Zm1-4a1 1 0 0 1-2 0V6a1 1 0 0 1 2 0v5Z';
                    toast.classList.remove('bg-white');
                    toast.classList.add('bg-yellow-600');
                }

                toastIcon.innerHTML = `
                <svg class="w-4 h-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 18 20">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="${iconPath}"/>
                </svg>
            `;
                toastIcon.className = `inline-flex items-center justify-center shrink-0 w-8 h-8 rounded-lg ${iconColor}`;

                toast.classList.remove('hidden');
                setTimeout(() => {
                    toast.classList.remove('opacity-0');
                    toast.classList.add('fade-in');
                }, 10);

                setTimeout(function() {
                    toast.classList.remove('fade-in');
                    toast.classList.add('fade-out');

                    setTimeout(() => {
                        toast.classList.add('hidden');
                    }, 500);
                }, 3000);
            }

            const closeButton = toast.querySelector('[data-dismiss-target="#toast-default"]');
            closeButton.addEventListener('click', function() {
                toast.classList.remove('fade-in');
                toast.classList.add('fade-out');
                setTimeout(() => {
                    toast.classList.add('hidden');
                }, 500);
            });
        });
    </script>
</body>

</html>