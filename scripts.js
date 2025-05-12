// Funkcija za dodavanje sastojka prilikom kreiranja recepta (recipe_create.php)
function addIngredientCreate() {
    const input = document.getElementById('ingredientInput');
    const quantityInput = document.getElementById('quantityInput');
    const value = input.value.trim();
    const quantity = quantityInput.value.trim();

    if (value) {
        // Prikaz sastojka i količine na stranici
        const selectedIngredients = document.getElementById('selectedIngredients');
        const span = document.createElement('span');
        span.className = 'selected-ingredient';
        span.innerHTML = `${value}${quantity ? ' (' + quantity + ')' : ''} <span class="remove-ingredient" onclick="removeIngredientCreate(this)">✕</span>`;
        selectedIngredients.appendChild(span);

        // Dodavanje skrivenih inputa za slanje serveru
        const ingredientsList = document.getElementById('ingredientsList');
        
        // Skriveni input za sastojak
        const hiddenInput = document.createElement('input');
        hiddenInput.type = 'hidden';
        hiddenInput.name = 'ingredients[]';
        hiddenInput.value = value;
        hiddenInput.dataset.value = value;
        ingredientsList.appendChild(hiddenInput);

        // Skriveni input za količinu (ako postoji)
        if (quantity) {
            const hiddenQuantity = document.createElement('input');
            hiddenQuantity.type = 'hidden';
            hiddenQuantity.name = 'quantities[]';
            hiddenQuantity.value = quantity;
            hiddenQuantity.dataset.value = value;
            ingredientsList.appendChild(hiddenQuantity);
        }

        input.value = '';
        quantityInput.value = '';
    }
}

function removeIngredientCreate(element) {
    const span = element.parentElement;
    const value = span.textContent.split('(')[0].replace('✕', '').trim();

    // Ukloni prikaz sastojka
    span.remove();

    // Ukloni odgovarajuće skrivene inpute
    const ingredientsList = document.getElementById('ingredientsList');
    const hiddenInputs = ingredientsList.querySelectorAll('input[name="ingredients[]"]');
    const hiddenQuantities = ingredientsList.querySelectorAll('input[name="quantities[]"]');
    
    hiddenInputs.forEach(input => {
        if (input.dataset.value === value) {
            input.remove();
        }
    });
    hiddenQuantities.forEach(input => {
        if (input.dataset.value === value) {
            input.remove();
        }
    });
}

// Funkcija za dodavanje sastojka u pretrazi (index.php)
function addIngredientSearch() {
    const input = document.getElementById('ingredientInput');
    const value = input.value.trim();

    if (value) {
        const selectedIngredients = document.getElementById('selectedIngredients');
        const span = document.createElement('span');
        span.className = 'selected-ingredient';
        span.innerHTML = `${value} <span class="remove-ingredient" onclick="this.parentElement.remove()">✕</span>`;
        selectedIngredients.appendChild(span);
        input.value = '';
    }
}

// Funkcije za modal (već postoje u tvom scripts.js)
function openSearchModal() {
    document.getElementById('searchModal').style.display = 'block';
}

function closeSearchModal() {
    document.getElementById('searchModal').style.display = 'none';
    document.getElementById('recipeName').value = '';
    document.getElementById('selectedIngredients').innerHTML = '';
}

function searchRecipes() {
    const name = document.getElementById('recipeName').value.trim();
    const ingredients = Array.from(document.querySelectorAll('#selectedIngredients .selected-ingredient')).map(span => span.textContent.replace('✕', '').trim());

    fetch(`/cook/search.php?name=${encodeURIComponent(name)}&ingredients=${encodeURIComponent(ingredients.join(','))}`)
        .then(response => response.json())
        .then(data => {
            const recipesDiv = document.getElementById('recipes');
            recipesDiv.innerHTML = '';
            if (data.length === 0) {
                recipesDiv.innerHTML = '<p class="text-center text-gray-400">Nema recepata za prikaz.</p>';
            } else {
                data.forEach(recipe => {
                    const avgRating = recipe.avg_rating || 0;
                    const ratingCount = recipe.rating_count || 0;
                    const recipeCard = `
                        <div class="recipe-card">
                            <img src="${recipe.image ? '/cook/uploads/' + recipe.image : 'https://via.placeholder.com/300x220.png?text=Nema+slike'}" alt="${recipe.title}">
                            <div class="recipe-card-content">
                                <h3>${recipe.title}</h3>
                                <p>${recipe.description ? recipe.description.substring(0, 100) + '...' : 'Nema opisa'}</p>
                                <div class="rating">
                                    ${[...Array(5)].map((_, i) => `
                                        <svg fill="${i < Math.round(avgRating) ? '#ffd700' : '#ccc'}" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.286 3.97a1 1 0 00.95.69h4.178c.969 0 1.371 1.24.588 1.81l-3.39 2.467a1 1 0 00-.364 1.118l1.286 3.97c.3.921-.755 1.688-1.54 1.118l-3.39-2.467a1 1 0 00-1.175 0l-3.39 2.467c-.784.57-1.838-.197-1.54-1.118l1.286-3.97a1 1 0 00-.364-1.118L2.737 9.397c-.783-.57-.38-1.81.588-1.81h4.178a1 1 0 00.95-.69l1.286-3.97z"/>
                                        </svg>
                                    `).join('')}
                                    <span>(${ratingCount})</span>
                                </div>
                                <a href="recipe_show.php?id=${recipe.id}">Pogledaj recept</a>
                            </div>
                        </div>
                    `;
                    recipesDiv.innerHTML += recipeCard;
                });
            }
            closeSearchModal();
        })
        .catch(error => console.error('Error:', error));
}

function generateStars(rating) {
    let stars = '';
    for (let i = 1; i <= 5; i++) {
        stars += `<svg fill="${i <= rating ? '#ffd700' : '#ccc'}" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.286 3.97a1 1 0 00.95.69h4.178c.969 0 1.371 1.24.588 1.81l-3.39 2.467a1 1 0 00-.364 1.118l1.286 3.97c.3.921-.755 1.688-1.54 1.118l-3.39-2.467a1 1 0 00-1.175 0l-3.39 2.467c-.784.57-1.838-.197-1.54-1.118l1.286-3.97a1 1 0 00-.364-1.118L2.737 9.397c-.783-.57-.38-1.81.588-1.81h4.178a1 1 0 00.95-.69l1.286-3.97z"/></svg>`;
    }
    return stars;
}