// Auto News Slider
let currentSlide = 0;
const slides = document.querySelectorAll('.slide');

function nextSlide() {
    slides[currentSlide].classList.remove('active');
    currentSlide = (currentSlide + 1) % slides.length;
    slides[currentSlide].classList.add('active');
}
setInterval(nextSlide, 5000);

// Profile Modal Logic
function openProfile(person) {
    const modal = document.getElementById('profileModal');
    const content = document.getElementById('modalData');
    
    content.innerHTML = `
        <img src="uploads/${person.img}" style="width:150px; height:150px; border-radius:50%; object-fit:cover; border:3px solid var(--primary)">
        <h2 style="margin:20px 0; color:var(--accent)">${person.name}</h2>
        <p style="background:var(--primary); display:inline-block; padding:5px 15px; border-radius:20px">${person.role}</p>
        <p style="margin-top:20px; line-height:1.6; color:#ccc">${person.bio || 'لا توجد نبذة حالياً'}</p>
    `;
    modal.style.display = "block";
}

function closeModal() {
    document.getElementById('profileModal').style.display = "none";
}

window.onclick = (event) => {
    if (event.target == document.getElementById('profileModal')) closeModal();
}