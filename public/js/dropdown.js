// Dropdown menu functionality
document.addEventListener('DOMContentLoaded', function() {
    const dropdownBtn = document.querySelector('.dropdown-btn');
    const dropdownMenu = document.querySelector('.dropdown-menu');
    
    if (dropdownBtn && dropdownMenu) {
        // Zmienne do śledzenia stanu menu
        let isMenuOpen = false;
        
        // Obsługa kliknięcia przycisku
        dropdownBtn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            isMenuOpen = !isMenuOpen;
            if (isMenuOpen) {
                dropdownMenu.classList.add('show');
            } else {
                dropdownMenu.classList.remove('show');
            }
        });
        
        // Zamknij menu po kliknięciu poza
        document.addEventListener('click', function(e) {
            if (!dropdownBtn.contains(e.target) && !dropdownMenu.contains(e.target)) {
                dropdownMenu.classList.remove('show');
                isMenuOpen = false;
            }
        });
        
        // Zapobiegaj zamknięciu menu przy kliknięciu w elementy menu
        dropdownMenu.addEventListener('click', function(e) {
            // Nie zamykaj menu, gdy klikamy na elementy menu
            // chyba że jest to link do wylogowania - wtedy chcemy, żeby wykonał akcję
            if (!e.target.closest('a[href="/logout"]')) {
                e.stopPropagation();
            }
        });
    }
}); 