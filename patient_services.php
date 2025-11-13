<?php
session_start();
if (!isset($_SESSION['email'])) {
    header('Location: index.php');
    exit();
}
include 'config.php';
?>

<div class="services-wrapper">
    <div class="services-header">
        <h2>Available Treatments</h2>
        <p>Discover our professional skincare services and book your next appointment.</p>
    </div>

    <div class="treatments-grid">
        <?php
        $query = "SELECT * FROM services WHERE status = 'available'";
        $result = mysqli_query($conn, $query);

        if (mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
                $service_id = $row['service_id'];
                $name = htmlspecialchars($row['name']);
                $description = htmlspecialchars($row['description']);
                $image = htmlspecialchars($row['image']);
                $price = htmlspecialchars($row['price']);
                echo "
                <div class='treatment-card' onclick=\"openServiceModal('$service_id', '$name', '$description', '$price', 'image/$image')\">
                    <img src='image/$image' alt='$name'>
                    <div class='overlay'>
                        <h3>$name</h3>
                        <p>₱$price</p>
                    </div>
                </div>
                ";
            }
        } else {
            echo '<p class="no-services">No services available at the moment.</p>';
        }
        ?>
    </div>
</div>

<!-- === MODAL POPUP === -->
<div id="serviceModal" class="modal">
    <div class="modal-content">
        <span class="close-btn" onclick="closeServiceModal()">&times;</span>
        <img id="modalImage" src="" alt="Service Image">
        <h2 id="modalName"></h2>
        <p id="modalDesc"></p>
        <p class="price" id="modalPrice"></p>
        <button id="bookButton" class="book-btn">Book Appointment</button>
    </div>
</div>

<script>
const serviceModal = document.getElementById('serviceModal');
const modalName = document.getElementById('modalName');
const modalDesc = document.getElementById('modalDesc');
const modalPrice = document.getElementById('modalPrice');
const modalImage = document.getElementById('modalImage');
const bookButton = document.getElementById('bookButton');
let selectedServiceId = null;

// open modal
function openServiceModal(id, name, desc, price, image) {
    serviceModal.style.display = 'flex';
    modalName.textContent = name;
    modalDesc.textContent = desc;
    modalPrice.textContent = "₱" + price;
    modalImage.src = image;
    selectedServiceId = id;
}

// close modal
function closeServiceModal() {
    serviceModal.style.display = 'none';
}

// redirect to booking page
bookButton.addEventListener('click', () => {
    if (selectedServiceId) {
        window.location.href = 'booking.php?service_id=' + selectedServiceId;
    }
});

// close when clicking outside
window.onclick = function(event) {
    if (event.target === serviceModal) {
        closeServiceModal();
    }
};
</script>

<style>
.services-wrapper {
    padding: 30px;
    font-family: 'Poppins', sans-serif;
}

.services-header {
    text-align: center;
    margin-bottom: 30px;
}

.services-header h2 {
    font-size: 28px;
    color: #333;
    margin-bottom: 5px;
}

.services-header p {
    color: #777;
    font-size: 15px;
}

.treatments-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
    gap: 25px;
}

.treatment-card {
    position: relative;
    border-radius: 15px;
    overflow: hidden;
    cursor: pointer;
    box-shadow: 0 4px 10px rgba(0,0,0,0.1);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.treatment-card img {
    width: 100%;
    height: 250px;
    object-fit: cover;
    transition: transform 0.4s ease;
}

.treatment-card:hover img {
    transform: scale(1.1);
}

.overlay {
    position: absolute;
    bottom: 0;
    width: 100%;
    background: rgba(0,0,0,0.55);
    color: #fff;
    padding: 12px;
    text-align: center;
    transition: background 0.3s ease;
}

.overlay h3 {
    margin: 0;
    font-size: 18px;
    font-weight: 600;
}

.overlay p {
    margin: 4px 0 0;
    font-size: 15px;
    color: #e0e0e0;
}

/* --- Modal --- */
.modal {
    display: none;
    position: fixed;
    z-index: 9999;
    top: 0; left: 0;
    width: 100%; height: 100%;
    background: rgba(0,0,0,0.6);
    justify-content: center;
    align-items: center;
    padding: 20px;
}

.modal-content {
    background: #fff;
    border-radius: 15px;
    max-width: 600px;
    width: 100%;
    padding: 25px;
    position: relative;
    animation: fadeIn 0.3s ease-in-out;
}

.modal-content img {
    width: 100%;
    border-radius: 10px;
    margin-bottom: 15px;
    object-fit: cover;
    max-height: 350px;
}

.modal-content h2 {
    color: #333;
    margin-bottom: 10px;
}

.modal-content p {
    color: #555;
    line-height: 1.5;
}

.modal-content .price {
    color: #80a833;
    font-weight: bold;
    margin-top: 10px;
    font-size: 18px;
}

.book-btn {
    display: inline-block;
    margin-top: 20px;
    background-color: #80a833;
    color: white;
    padding: 10px 25px;
    border-radius: 8px;
    border: none;
    cursor: pointer;
    transition: 0.2s;
}

.book-btn:hover {
    background-color: #6a8f2c;
}

.close-btn {
    position: absolute;
    top: 12px;
    right: 18px;
    font-size: 26px;
    color: #666;
    cursor: pointer;
}

.close-btn:hover {
    color: #000;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(-10px); }
    to { opacity: 1; transform: translateY(0); }
}

.no-services {
    text-align: center;
    color: #777;
    margin-top: 40px;
}
</style>
