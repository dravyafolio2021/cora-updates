const fs = require('fs');
const path = '/Users/shrutian/Desktop/cora/app/public/wp-content/plugins/cora-studio-ai-locked/assets/js/admin-script.js';
let content = fs.readFileSync(path, 'utf8');

// 1. Add coraInitOfficeLocationBtn in DOMContentLoaded
content = content.replace(
    `setTimeout(coraInitAttendance, 1000);`,
    `setTimeout(coraInitAttendance, 1000);\n    coraInitOfficeLocationBtn();`
);

// 2. Add coraInitOfficeLocationBtn function
const initFn = `
window.coraInitOfficeLocationBtn = function() {
    fetch(coraData.ajaxUrl + '?action=cora_get_office_location&nonce=' + coraData.ajaxNonce)
    .then(r => r.json())
    .then(res => {
        if (res.success && res.data.name) {
            const btnText = document.getElementById('cora-office-btn-text');
            if (btnText) btnText.innerText = 'Office: ' + res.data.name.substring(0, 15) + (res.data.name.length > 15 ? '...' : '');
        }
    });
};
`;
content = content.replace(
    `window.coraToggleOfficeLocationDrawer = function(show) {`,
    initFn + `\nwindow.coraToggleOfficeLocationDrawer = function(show) {`
);

// 3. Update coraToggleOfficeLocationDrawer to show search history and set address name
content = content.replace(
    `const lng = parseFloat(res.data.lng);
                        coraSetMapMarker(lat, lng);`,
    `const lng = parseFloat(res.data.lng);
                        coraSetMapMarker(lat, lng);
                        if (res.data.name) document.getElementById('cora-office-address-search').value = res.data.name;`
);

// Add history rendering on open
content = content.replace(
    `drawer.classList.remove('translate-x-full');
        // Initialize Map`,
    `drawer.classList.remove('translate-x-full');
        coraRenderSearchHistory();
        // Initialize Map`
);

// 4. Update coraSearchOfficeLocation for regex and history
const searchFnReplace = `window.coraSearchOfficeLocation = function(searchQuery) {
    const query = searchQuery || document.getElementById('cora-office-address-search').value;
    if (!query) return;

    // Check if it's a coordinate string or Google Maps URL with coordinates
    const latLngMatch = query.match(/@(-?\\d+\\.\\d+),(-?\\d+\\.\\d+)/) || query.match(/^(-?\\d+\\.\\d+)[\\s,]+(-?\\d+\\.\\d+)$/);
    if (latLngMatch) {
        const lat = parseFloat(latLngMatch[1]);
        const lng = parseFloat(latLngMatch[2]);
        coraSetMapMarker(lat, lng);
        document.getElementById('cora-office-address-search').value = query;
        coraAddSearchHistory(query, lat, lng);
        document.getElementById('cora-office-search-results').classList.add('hidden');
        return;
    }
    
    // Add &addressdetails=1 to try and get better fuzzy matching sometimes, though Nominatim is strict
    // Add &countrycodes=in to restrict search to India only
    fetch('https://nominatim.openstreetmap.org/search?format=json&countrycodes=in&q=' + encodeURIComponent(query))
    .then(r => r.json())
    .then(data => {
        const resultsContainer = document.getElementById('cora-office-search-results');
        resultsContainer.innerHTML = '';
        if (data.length === 0) {
            resultsContainer.innerHTML = '<div class="p-3 text-xs text-zinc-500">No exact results found. Try simplifying your query.</div>';
        } else {
            data.forEach(item => {
                const div = document.createElement('div');
                div.className = "p-2 hover:bg-zinc-50 text-xs border-b border-zinc-100 cursor-pointer text-zinc-700";
                div.innerText = item.display_name;
                div.onclick = () => {
                    coraSetMapMarker(parseFloat(item.lat), parseFloat(item.lon));
                    resultsContainer.classList.add('hidden');
                    document.getElementById('cora-office-address-search').value = item.display_name;
                    coraAddSearchHistory(item.display_name, item.lat, item.lon);
                };
                resultsContainer.appendChild(div);
            });
        }
        resultsContainer.classList.remove('hidden');
    })
    .catch(err => console.error(err));
}

window.coraAddSearchHistory = function(name, lat, lng) {
    let history = JSON.parse(localStorage.getItem('cora_office_search_history') || '[]');
    // Remove if already exists
    history = history.filter(h => h.name !== name);
    // Add to front
    history.unshift({name, lat, lng});
    if (history.length > 5) history.pop();
    localStorage.setItem('cora_office_search_history', JSON.stringify(history));
    coraRenderSearchHistory();
};

window.coraRenderSearchHistory = function() {
    let history = JSON.parse(localStorage.getItem('cora_office_search_history') || '[]');
    let container = document.getElementById('cora-office-search-history');
    
    if (!container) {
        // Create it if it doesn't exist
        const searchDiv = document.getElementById('cora-office-address-search').parentNode.parentNode;
        container = document.createElement('div');
        container.id = 'cora-office-search-history';
        container.className = 'flex flex-wrap gap-2 mt-2';
        searchDiv.appendChild(container);
    }
    
    if (history.length === 0) {
        container.innerHTML = '';
        return;
    }
    
    container.innerHTML = '<span class="text-[10px] text-zinc-400 w-full mb-1">Recent Searches:</span>' + history.map(h => {
        return \`<span class="text-[10px] bg-zinc-100 border border-zinc-200 text-zinc-600 px-2 py-1 rounded cursor-pointer hover:bg-zinc-200 transition-colors truncate max-w-[150px]" onclick="coraSetMapMarker(\${h.lat}, \${h.lng}); document.getElementById('cora-office-address-search').value = '\${h.name.replace(/'/g, "\\\\'")}'">\${h.name}</span>\`;
    }).join('');
};
`;

content = content.replace(/window\.coraSearchOfficeLocation = function\(searchQuery\) \{[\s\S]*?\}\n/, searchFnReplace);


// 5. Update coraSaveOfficeLocationDrawer to pass name
content = content.replace(
    `formData.append('lng', lng);`,
    `formData.append('lng', lng);\n    const name = document.getElementById('cora-office-address-search').value;\n    if (name) formData.append('name', name);`
);

// 6. Update coraSaveOfficeLocationDrawer to call coraInitOfficeLocationBtn after save
content = content.replace(
    `window.coraShowToast("Office location saved.");
            coraToggleOfficeLocationDrawer(false);`,
    `window.coraShowToast("Office location saved.");
            coraToggleOfficeLocationDrawer(false);
            coraInitOfficeLocationBtn();`
);

fs.writeFileSync(path, content);
console.log("Patched successfully");
