<?php
session_start();


// Asegúrate de que esta línea esté al principio de tu archivo
include('scripts/conexion.php'); // Ajusta la ruta según sea necesario


// Consulta para obtener los slides del carrusel
$result = $conn->query("SELECT * FROM carousel ORDER BY order_index ASC");
?>


<!DOCTYPE html>
<html lang="es">
  <head>
    <title>Corsacor - Instituto Educativo Sagrado Corazón de Jesús</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="Instituto Educativo Sagrado Corazón de Jesús - Cursos y educación de calidad en Cúcuta">
    <meta name="keywords" content="educación, cursos, instituto, Cúcuta, Norte de Santander">
    
    <!-- Preload critical resources -->
    <link rel="preload" href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" as="style">
    <link rel="preload" href="css/style.css" as="style">
    
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Icons and Styles -->
    <link rel="stylesheet" href="css/open-iconic-bootstrap.min.css">
    <link rel="stylesheet" href="css/animate.css">
    <link rel="stylesheet" href="css/owl.carousel.min.css">
    <link rel="stylesheet" href="css/owl.theme.default.min.css">
    <link rel="stylesheet" href="css/magnific-popup.css">
    <link rel="stylesheet" href="css/aos.css">
    <link rel="stylesheet" href="css/ionicons.min.css">
    <link rel="stylesheet" href="css/flaticon.css">
    <link rel="stylesheet" href="css/icomoon.css">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="./dist/css/styles.css">
    
    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="js/inscripcion-handler.js"></script>
    
    <!-- Custom Modern Styles -->
    <style>
      :root {
        --primary-color: #1e3a8a;
        --secondary-color: #6c757d;
        --accent-color: #1e40af;
        --text-dark: #1e3a8a;
        --text-light: #6c757d;
        --bg-light: #f8f9fa;
        --shadow-light: 0 2px 10px rgba(0,0,0,0.1);
        --shadow-medium: 0 4px 20px rgba(0,0,0,0.15);
        --shadow-heavy: 0 8px 30px rgba(0,0,0,0.2);
        --border-radius: 12px;
        --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
      }
      
      /* Smooth scrolling */
      html {
        scroll-behavior: smooth;
      }
      
      /* Modern navbar */
      .navbar {
        backdrop-filter: blur(10px);
        background: rgba(255, 255, 255, 0.95) !important;
        box-shadow: var(--shadow-light);
        transition: var(--transition);
      }
      
      .navbar.scrolled {
        background: rgba(255, 255, 255, 0.98) !important;
        box-shadow: var(--shadow-medium);
      }
      
      .navbar-brand {
        font-weight: 700;
        font-size: 1.5rem;
        color: var(--primary-color) !important;
      }
      
      .nav-link {
        font-weight: 500;
        color: var(--text-dark) !important;
        transition: var(--transition);
        position: relative;
      }
      
      .nav-link:hover {
        color: var(--primary-color) !important;
        transform: translateY(-2px);
      }
      
      .nav-link::after {
        content: '';
        position: absolute;
        width: 0;
        height: 2px;
        bottom: -5px;
        left: 50%;
        background: var(--primary-color);
        transition: var(--transition);
        transform: translateX(-50%);
      }
      
      .nav-link:hover::after {
        width: 100%;
      }
      
      /* Modern hero section */
      .hero {
        position: relative;
        overflow: hidden;
      }
      
      .slider-item {
        position: relative;
        min-height: 100vh;
        display: flex;
        align-items: center;
      }
      
      .slider-item::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(30, 58, 138, 0.8);
        z-index: 1;
      }
      
      .slider-item .container {
        position: relative;
        z-index: 2;
      }
      
      .hero-content {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(10px);
        border-radius: var(--border-radius);
        padding: 3rem;
        box-shadow: var(--shadow-heavy);
        margin: 2rem 0;
      }
      
      .hero-title {
        font-size: 3.5rem;
        font-weight: 800;
        color: var(--text-dark);
        margin-bottom: 1rem;
        line-height: 1.2;
      }
      
      .hero-subtitle {
        font-size: 1.25rem;
        color: var(--text-light);
        margin-bottom: 2rem;
        font-weight: 400;
      }
      
      .hero-description {
        font-size: 1.1rem;
        color: var(--text-dark);
        margin-bottom: 2rem;
        line-height: 1.6;
      }
      
      .cta-button {
        background: var(--primary-color);
        border: none;
        padding: 1rem 2.5rem;
        border-radius: 50px;
        color: white;
        font-weight: 600;
        font-size: 1.1rem;
        text-decoration: none;
        display: inline-block;
        transition: var(--transition);
        box-shadow: var(--shadow-medium);
      }
      
      .cta-button:hover {
        transform: translateY(-3px);
        box-shadow: var(--shadow-heavy);
        color: white;
        text-decoration: none;
      }
      
      /* Modern course cards */
      .course-card {
        background: white;
        border-radius: var(--border-radius);
        box-shadow: var(--shadow-light);
        transition: var(--transition);
        overflow: hidden;
        height: 100%;
        border: 1px solid rgba(0,0,0,0.05);
        display: flex;
        flex-direction: column;
        padding: 1.5rem 0 0 0;
      }
      
      .course-card:hover {
        transform: translateY(-10px);
        box-shadow: var(--shadow-heavy);
      }
      
      .course-icon {
        width: 100px;
        height: 100px;
        background: var(--primary-color);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 2rem auto 2rem;
        transition: var(--transition);
        padding: 1rem;
      }
      
      .course-card:hover .course-icon {
        transform: scale(1.1) rotate(5deg);
      }
      
      .course-icon i {
        font-size: 2.5rem;
        color: white;
      }
      
      .course-icon img {
        width: 60px;
        height: 60px;
        object-fit: contain;
        background: rgba(255, 255, 255, 0.9);
        border-radius: 8px;
        padding: 8px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
      }
      
      .course-title {
        font-size: 1.5rem;
        font-weight: 700;
        color: var(--text-dark);
        margin-bottom: 1rem;
        text-align: center;
      }
      
      .course-info {
        color: var(--text-light);
        margin-bottom: 0.5rem;
        font-size: 0.95rem;
      }
      
      .course-info strong {
        color: var(--text-dark);
        font-weight: 600;
      }
      
      .inscribirse-btn {
        background: var(--primary-color);
        border: none;
        padding: 0.75rem 2rem;
        border-radius: 25px;
        color: white;
        font-weight: 600;
        transition: var(--transition);
        width: 100%;
        margin-top: 1rem;
      }
      
      .inscribirse-btn:hover {
        transform: translateY(-2px);
        box-shadow: var(--shadow-medium);
        color: white;
      }
      
      /* Modern resume section */
      .resume-card {
        background: white;
        border-radius: var(--border-radius);
        padding: 2rem;
        box-shadow: var(--shadow-light);
        transition: var(--transition);
        border-left: 4px solid var(--primary-color);
        margin-bottom: 2rem;
      }
      
      .resume-card:hover {
        transform: translateX(10px);
        box-shadow: var(--shadow-medium);
      }
      
      .resume-date {
        background: var(--primary-color);
        color: white;
        padding: 0.5rem 1rem;
        border-radius: 20px;
        font-weight: 600;
        display: inline-block;
        margin-bottom: 1rem;
      }
      
      .resume-title {
        font-size: 1.5rem;
        font-weight: 700;
        color: var(--text-dark);
        margin-bottom: 0.5rem;
      }
      
      .resume-position {
        color: var(--primary-color);
        font-weight: 600;
        margin-bottom: 1rem;
      }
      
      /* Modern contact section */
      .contact-card {
        background: white;
        border-radius: var(--border-radius);
        padding: 2rem;
        text-align: center;
        box-shadow: var(--shadow-light);
        transition: var(--transition);
        height: 100%;
      }
      
      .contact-card:hover {
        transform: translateY(-5px);
        box-shadow: var(--shadow-medium);
      }
      
      .contact-icon {
        width: 60px;
        height: 60px;
        background: var(--primary-color);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 1rem;
        transition: var(--transition);
      }
      
      .contact-card:hover .contact-icon {
        transform: scale(1.1);
      }
      
      .contact-icon i {
        font-size: 1.5rem;
        color: white;
      }
      
      .contact-title {
        font-size: 1.25rem;
        font-weight: 700;
        color: var(--text-dark);
        margin-bottom: 0.5rem;
      }
      
      .contact-info {
        color: var(--text-light);
        font-size: 1rem;
      }
      
      .contact-info a {
        color: var(--primary-color);
        text-decoration: none;
        transition: var(--transition);
      }
      
      .contact-info a:hover {
        color: var(--primary-color);
        text-decoration: none;
      }
      
      /* Section headers */
      .section-header {
        text-align: center;
        margin-bottom: 4rem;
      }
      
      .section-title {
        font-size: 3rem;
        font-weight: 800;
        color: var(--text-dark);
        margin-bottom: 1rem;
        position: relative;
      }
      
      .section-title::after {
        content: '';
        position: absolute;
        bottom: -10px;
        left: 50%;
        transform: translateX(-50%);
        width: 80px;
        height: 4px;
        background: var(--primary-color);
        border-radius: 2px;
      }
      
      .section-subtitle {
        font-size: 1.25rem;
        color: var(--text-light);
        max-width: 600px;
        margin: 0 auto;
        line-height: 1.6;
      }
      
      /* Responsive design */
      @media (max-width: 768px) {
        .hero-title {
          font-size: 2.5rem;
        }
        
        .hero-content {
          padding: 2rem;
          margin: 1rem 0;
        }
        
        .section-title {
          font-size: 2.5rem;
        }
        
        .course-card {
          margin-bottom: 2rem;
        }
      }
      
      /* Loading animation */
      .fade-in {
        animation: fadeIn 0.8s ease-in-out;
      }
      
      @keyframes fadeIn {
        from {
          opacity: 0;
          transform: translateY(30px);
        }
        to {
          opacity: 1;
          transform: translateY(0);
        }
      }
      
      /* Smooth transitions for all elements */
      * {
        transition: var(--transition);
      }
    </style>
  </head>
  <body data-spy="scroll" data-target=".site-navbar-target" data-offset="300">
	  
	  
    <nav class="navbar navbar-expand-lg navbar-dark ftco_navbar ftco-navbar-light site-navbar-target" id="ftco-navbar">
	    <div class="container">
	      <a class="navbar-brand" href="#home-section" class="nav-link">Corsacor</a>
	      <button class="navbar-toggler js-fh5co-nav-toggle fh5co-nav-toggle" type="button" data-toggle="collapse" data-target="#ftco-nav" aria-controls="ftco-nav" aria-expanded="false" aria-label="Toggle navigation">
	        <span class="oi oi-menu"></span> Menu
	      </button>

	      <div class="collapse navbar-collapse" id="ftco-nav">
	        <ul class="navbar-nav nav ml-auto">
	          <li class="nav-item"><a href="#home-section" class="nav-link"><span>Inicio</span></a></li>
			  <li class="nav-item"><a href="#services-section" class="nav-link"><span>Cursos</span></a></li>
	          <li class="nav-item"><a href="#resume-section" class="nav-link"><span>Resumen</span></a></li>
	          <li class="nav-item"><a href="#contact-section" class="nav-link"><span>Contactos</span></a></li>
			  <li class="nav-item"><a href="/login/login.php" class="nav-link" target= "_blank"><span>Login</span></a></li>
	        </ul>
	      </div>
	    </div>
	  </nav>
    <!-- Hero Section with Modern Design -->
	  <section id="home-section" class="hero">
	  <div class="home-slider owl-carousel">
    <?php while ($row = $result->fetch_assoc()): ?>
            <div class="slider-item" style="background-image: url(uploads/<?php echo $row['image']; ?>); background-size: cover; background-position: center;">
        <div class="container"> 
                    <div class="row align-items-center min-vh-100">
                        <div class="col-lg-6">
                            <div class="hero-content fade-in">
                                <div class="hero-subtitle">
                                    <i class="icon-calendar mr-2"></i>
                                    Inicio: <?= $row['fecha_curso_inicio'] ?> | Fin: <?= $row['fecha_curso_fin'] ?>
                                </div>
                                <h1 class="hero-title"><?php echo $row['title']; ?></h1>
                                <p class="hero-description"><?php echo $row['description']; ?></p>
                                <a href="#services-section" class="cta-button">
                                    <i class="icon-arrow-right mr-2"></i>
                                    Ver Cursos Disponibles
                                </a>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="hero-image-container text-center">
                                <div class="hero-image-placeholder">
                                    <i class="icon-graduation-cap" style="font-size: 8rem; color: rgba(255,255,255,0.3);"></i>
                </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php endwhile; ?>
</div>	
    </section>

    <!-- Modern Courses Section -->
    <section class="ftco-section" id="services-section" style="background: #f8f9fa;">
    	<div class="container">
            <div class="section-header">
                <h1 class="section-title">Nuestros Cursos</h1>
                <p class="section-subtitle">Descubre nuestra amplia gama de cursos diseñados para potenciar tu educación y desarrollo profesional</p>
          </div>
            
    		<div class="row">
<?php
$sql = "SELECT id_curso, nombre_curso, descripcion, nivel_educativo, duracion, icono FROM cursos";
$result = $conn->query($sql);
                
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                        echo '<div class="col-lg-4 col-md-6 mb-4">';
                        echo '  <div class="course-card fade-in">';
                        echo '      <div class="course-icon">';
                if (!empty($row["icono"])) {
                            echo '          <img src="uploads/icons/' . htmlspecialchars($row["icono"]) . '" alt="Icono del curso">';
                } else {
                    echo '          <i class="flaticon-analysis"></i>';
                }
                        echo '      </div>';
                        echo '      <div class="p-4 flex-grow-1 d-flex flex-column">';
                        echo '          <h3 class="course-title">' . htmlspecialchars($row["nombre_curso"]) . '</h3>';
                        echo '          <div class="course-info flex-grow-1">';
                        echo '              <p><strong>Categoría:</strong> ' . htmlspecialchars($row["descripcion"]) . '</p>';
                        echo '              <p><strong>Nivel:</strong> ' . htmlspecialchars(ucfirst($row["nivel_educativo"])) . '</p>';
                        echo '              <p><strong>Duración:</strong> ' . htmlspecialchars($row["duracion"]) . ' semanas</p>';
                        echo '          </div>';
                        echo '          <button class="inscribirse-btn mt-auto" data-curso-id="' . htmlspecialchars($row["id_curso"]) . '">';
                        echo '              <i class="icon-user-plus mr-2"></i>';
                        echo '              Inscribirse Ahora';
                        echo '          </button>';
                echo '      </div>';
                echo '  </div>';
                echo '</div>';
            }
        } else {
                    echo '<div class="col-12 text-center">';
                    echo '  <div class="course-card">';
                    echo '      <div class="p-5">';
                    echo '          <i class="icon-info-circle" style="font-size: 3rem; color: var(--text-light); margin-bottom: 1rem;"></i>';
                    echo '          <h3>No hay cursos disponibles</h3>';
                    echo '          <p>Próximamente tendremos nuevos cursos disponibles. ¡Mantente atento!</p>';
                    echo '      </div>';
                    echo '  </div>';
                    echo '</div>';
        }
        ?>
    </div>
</div>
    </section>

    <!-- Modern Resume Section -->
<?php
// Incluir el archivo de conexión
include('scripts/conexion.php'); // Asegúrate de que aquí no se cierre $conn

// Consulta SQL para obtener todos los cursos de la tabla resume_cursos
$sql = "SELECT * FROM resume_cursos";
$result = $conn->query($sql);

// Verificar si la consulta fue exitosa
if (!$result) {
    die("Error en la consulta: " . $conn->error);
}
?>

    <section class="ftco-section ftco-no-pb" id="resume-section" style="background: white;">
    <div class="container">
            <div class="section-header">
                <h1 class="section-title">Resumen Institucional</h1>
                <p class="section-subtitle">Conoce más sobre nuestra institución y los valores que nos caracterizan como el Colegio Sagrado Corazón de Jesús</p>
            </div>
            
        <div class="row">
            <?php if ($result->num_rows > 0): ?>
                <?php while($row = $result->fetch_assoc()): ?>
                        <div class="col-lg-6 col-md-12 mb-4">
                            <div class="resume-card fade-in">
                                <div class="resume-date">
                                    <i class="icon-calendar mr-2"></i>
                                    <?php echo htmlspecialchars($row['dia']); ?>
                                </div>
                                <h2 class="resume-title"><?php echo htmlspecialchars($row['nombre']); ?></h2>
                                <div class="resume-position">
                                    <i class="icon-location mr-2"></i>
                                    <?php echo htmlspecialchars($row['lugar']); ?>
                                </div>
                                <p class="mt-3"><?php echo htmlspecialchars($row['descripcion']); ?></p>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                    <div class="col-12 text-center">
                        <div class="resume-card">
                            <div class="p-5">
                                <i class="icon-info-circle" style="font-size: 3rem; color: var(--text-light); margin-bottom: 1rem;"></i>
                                <h3>Información Institucional</h3>
                                <p>Próximamente tendremos más información sobre nuestra institución disponible.</p>
                            </div>
                        </div>
                    </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<?php
// Verifica que $conn no haya sido cerrado antes, y ciérralo aquí si es necesario
if ($conn) {
    $conn->close(); // Solo cerrar si no se cerró antes
}
?>

    <!-- Modern Contact Section -->
    <section class="ftco-section contact-section ftco-no-pb" id="contact-section" style="background: #f8f9fa;">
      <div class="container">
            <div class="section-header">
                <h1 class="section-title">Contáctanos</h1>
                <p class="section-subtitle">Estamos aquí para ayudarte. Comunícate con nosotros a través de cualquiera de nuestros canales de contacto</p>
        </div>

            <div class="row">
                <div class="col-lg-3 col-md-6 mb-4">
                    <div class="contact-card fade-in">
                        <div class="contact-icon">
                            <i class="icon-user"></i>
          		</div>
                        <h3 class="contact-title">Secretaria</h3>
                        <p class="contact-info">Edy Montañez</p>
	          </div>
          </div>

                <div class="col-lg-3 col-md-6 mb-4">
                    <div class="contact-card fade-in">
                        <div class="contact-icon">
                            <i class="icon-phone2"></i>
          		</div>
                        <h3 class="contact-title">Teléfono</h3>
                        <p class="contact-info">
                            <a href="tel://5503455">5503455 Ext. 101</a>
                        </p>
	          </div>
          </div>
                
                <div class="col-lg-3 col-md-6 mb-4">
                    <div class="contact-card fade-in">
                        <div class="contact-icon">
                            <i class="icon-paper-plane"></i>
          		</div>
                        <h3 class="contact-title">Email</h3>
                        <p class="contact-info">
                            <a href="mailto:secretaria.rectoria@corsaje.edu.co">secretaria.rectoria@corsaje.edu.co</a>
                        </p>
	          </div>
          </div>
                
                <div class="col-lg-3 col-md-6 mb-4">
                    <div class="contact-card fade-in">
                        <div class="contact-icon">
                            <i class="icon-globe"></i>
          		</div>
                        <h3 class="contact-title">Sitio Web</h3>
                        <p class="contact-info">
                            <a href="https://www.corsaje.edu.co/" target="_blank">www.corsaje.edu.co</a>
                        </p>
	          </div>
          </div>
        </div>
            
            <!-- Additional Contact Information -->
            <div class="row mt-5">
                <div class="col-12">
                    <div class="contact-card text-center">
                        <div class="p-4">
                            <h3 class="contact-title mb-3">
                                <i class="icon-location mr-2"></i>
                                Ubicación
                            </h3>
                            <p class="contact-info mb-3">
                                Calle 16 #3-60 La Playa, Cúcuta - Norte de Santander
                            </p>
                            <p class="contact-info">
                                <strong>Horario de atención:</strong> Lunes a Viernes de 7:00 AM a 5:00 PM
                            </p>
                        </div>
          		</div>
	          </div>
          </div>
        </div>
    </section>

      
    <footer class="ftco-footer ftco-section">
      <div class="container">
        <div class="row mb-5">
          <div class="col-md">
            <div class="ftco-footer-widget mb-4">
              <h2 class="ftco-heading-2">I.E. SAGRADO CORAZÓN DE JESÚS</h2>
              <p>JUNTOS CONSTRUIMOS EL CORSAJE QUE DESEAMOS</p>
              <ul class="ftco-footer-social list-unstyled float-md-left float-lft mt-5">
          
                <li class="ftco-animate"><a href="https://www.facebook.com/corsajelasalle?_rdc=1&_rdr"><span class="icon-facebook"></span></a></li>
                <li class="ftco-animate"><a href="https://www.instagram.com/corsacor_c/"><span class="icon-instagram"></span></a></li>
              </ul>
            </div>
          </div>
          <div class="col-md">
            <div class="ftco-footer-widget mb-4 ml-md-4">
              <h2 class="ftco-heading-2">Links</h2>
              <ul class="list-unstyled">
                <li><a href="#"><span class="icon-long-arrow-right mr-2"></span>Inicio</a></li>
                <li><a href="#"><span class="icon-long-arrow-right mr-2"></span>Acerca De</a></li>
                <li><a href="#"><span class="icon-long-arrow-right mr-2"></span>Cursos</a></li>
                <li><a href="#"><span class="icon-long-arrow-right mr-2"></span>Proyectos</a></li>
                <li><a href="#"><span class="icon-long-arrow-right mr-2"></span>Contáctanos</a></li>
              </ul>
            </div>
          </div>
          <div class="col-md">
             <div class="ftco-footer-widget mb-4">
              <h2 class="ftco-heading-2">Servicios</h2>
              <ul class="list-unstyled">
                <li><a href="https://www.corsaje.edu.co/wp-content/uploads/2022/01/PLAN-DE-ESTUDIOS-2022..pdf"><span class="icon-long-arrow-right mr-2"></span>Plan Estudios</a></li>
                <li><a href="https://www.corsaje.edu.co/area-tecnica/"><span class="icon-long-arrow-right mr-2"></span>Area Tecnica</a></li>
                <li><a href="https://www.corsaje.edu.co/category/pastoral/"><span class="icon-long-arrow-right mr-2"></span>Pastoral</a></li>
                <li><a href="https://www.corsaje.edu.co/category/complementarias/"><span class="icon-long-arrow-right mr-2"></span>Actividades Complementarias</a></li>

              </ul>
            </div>
          </div>
          <div class="col-md">
            <div class="ftco-footer-widget mb-4">
            	<h2 class="ftco-heading-2">Have a Questions?</h2>
            	<div class="block-23 mb-3">
	              <ul>
	                <li><span class="icon icon-map-marker"></span><span class="text">Calle 16 #3-60 La Playa, Cúcuta - Norte de Santander</span></li>
	                <li><a href="#"><span class="icon icon-phone"></span><span class="text">5503455</span></a></li>
	                <li><a href="#"><span class="icon icon-envelope"></span><span class="text"> corsaje@corsaje.edu.co </span></a></li>
	              </ul>
	            </div>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-md-12 text-center">

            <p><!-- Link back to Colorlib can't be removed. Template is licensed under CC BY 3.0. -->
  Copyright &copy;<script>document.write(new Date().getFullYear());</script> I.E. SAGRADO CORAZÓN DE JESÚS <i class="icon-heart color-danger" aria-hidden="true"></i> by <a href="https://colorlib.com" target="_blank">Capon y Camilo</a>
  <!-- Link back to Colorlib can't be removed. Template is licensed under CC BY 3.0. --></p>
          </div>
        </div>
      </div>
    </footer>
    
  

  <!-- loader -->
  <div id="ftco-loader" class="show fullscreen"><svg class="circular" width="48px" height="48px"><circle class="path-bg" cx="24" cy="24" r="22" fill="none" stroke-width="4" stroke="#eeeeee"/><circle class="path" cx="24" cy="24" r="22" fill="none" stroke-width="4" stroke-miterlimit="10" stroke="#F96D00"/></svg></div>

  <script src="js/jquery.min.js"></script>
  <script src="js/jquery-migrate-3.0.1.min.js"></script>
  <script src="js/popper.min.js"></script>
  <script src="js/bootstrap.min.js"></script>
  <script src="js/jquery.easing.1.3.js"></script>
  <script src="js/jquery.waypoints.min.js"></script>
  <script src="js/jquery.stellar.min.js"></script>
  <script src="js/owl.carousel.min.js"></script>
  <script src="js/jquery.magnific-popup.min.js"></script>
  <script src="js/aos.js"></script>
  <script src="js/jquery.animateNumber.min.js"></script>
  <script src="js/scrollax.min.js"></script>
  <script src="js/main.js"></script>
  
  <!-- Modern JavaScript for enhanced functionality -->
  <script>
    $(document).ready(function() {
      // Navbar scroll effect
      $(window).scroll(function() {
        if ($(this).scrollTop() > 50) {
          $('.navbar').addClass('scrolled');
        } else {
          $('.navbar').removeClass('scrolled');
        }
      });

      // Smooth scrolling for anchor links
      $('a[href^="#"]').on('click', function(event) {
        var target = $(this.getAttribute('href'));
        if (target.length) {
          event.preventDefault();
          $('html, body').stop().animate({
            scrollTop: target.offset().top - 80
          }, 1000, 'easeInOutExpo');
        }
      });

      // Fade in animation on scroll
      function fadeInOnScroll() {
        $('.fade-in').each(function() {
          var elementTop = $(this).offset().top;
          var elementBottom = elementTop + $(this).outerHeight();
          var viewportTop = $(window).scrollTop();
          var viewportBottom = viewportTop + $(window).height();

          if (elementBottom > viewportTop && elementTop < viewportBottom) {
            $(this).addClass('animate__animated animate__fadeInUp');
          }
        });
      }

      // Run on scroll
      $(window).on('scroll', fadeInOnScroll);
      
      // Run on load
      fadeInOnScroll();

      // Course card hover effects
      $('.course-card').hover(
        function() {
          $(this).find('.course-icon').addClass('animate__animated animate__pulse');
        },
        function() {
          $(this).find('.course-icon').removeClass('animate__animated animate__pulse');
        }
      );

      // Contact card hover effects
      $('.contact-card').hover(
        function() {
          $(this).find('.contact-icon').addClass('animate__animated animate__bounce');
        },
        function() {
          $(this).find('.contact-icon').removeClass('animate__animated animate__bounce');
        }
      );

      // Resume card hover effects
      $('.resume-card').hover(
        function() {
          $(this).addClass('animate__animated animate__pulse');
        },
        function() {
          $(this).removeClass('animate__animated animate__pulse');
        }
      );

      // CTA button click effect
      $('.cta-button, .inscribirse-btn').on('click', function(e) {
        // Create ripple effect
        var button = $(this);
        var ripple = $('<span class="ripple"></span>');
        var rect = this.getBoundingClientRect();
        var size = Math.max(rect.width, rect.height);
        var x = e.clientX - rect.left - size / 2;
        var y = e.clientY - rect.top - size / 2;
        
        ripple.css({
          width: size,
          height: size,
          left: x,
          top: y
        });
        
        button.append(ripple);
        
        setTimeout(function() {
          ripple.remove();
        }, 600);
      });

      // Parallax effect for hero section
      $(window).scroll(function() {
        var scrolled = $(this).scrollTop();
        var parallax = $('.slider-item');
        var speed = scrolled * 0.5;
        parallax.css('transform', 'translateY(' + speed + 'px)');
      });

      // Initialize tooltips
      $('[data-toggle="tooltip"]').tooltip();

      // Add loading animation to buttons
      $('.inscribirse-btn').on('click', function() {
        var btn = $(this);
        var originalText = btn.html();
        
        btn.html('<i class="icon-spinner animate-spin mr-2"></i>Cargando...');
        btn.prop('disabled', true);
        
        // Simulate loading (replace with actual AJAX call)
        setTimeout(function() {
          btn.html(originalText);
          btn.prop('disabled', false);
        }, 2000);
      });

      // Mobile menu improvements
      $('.navbar-toggler').on('click', function() {
        $(this).toggleClass('active');
        $('.navbar-collapse').toggleClass('show');
      });

      // Close mobile menu when clicking on a link
      $('.navbar-nav .nav-link').on('click', function() {
        $('.navbar-collapse').removeClass('show');
        $('.navbar-toggler').removeClass('active');
      });

      // Add scroll indicator
      $(window).scroll(function() {
        var scrollTop = $(this).scrollTop();
        var docHeight = $(document).height();
        var winHeight = $(this).height();
        var scrollPercent = (scrollTop / (docHeight - winHeight)) * 100;
        
        $('.scroll-indicator').css('width', scrollPercent + '%');
      });

      // Lazy loading for images
      if ('IntersectionObserver' in window) {
        const imageObserver = new IntersectionObserver((entries, observer) => {
          entries.forEach(entry => {
            if (entry.isIntersecting) {
              const img = entry.target;
              img.src = img.dataset.src;
              img.classList.remove('lazy');
              imageObserver.unobserve(img);
            }
          });
        });

        document.querySelectorAll('img[data-src]').forEach(img => {
          imageObserver.observe(img);
        });
      }
    });

    // Add CSS for ripple effect
    const style = document.createElement('style');
    style.textContent = `
      .ripple {
        position: absolute;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.6);
        transform: scale(0);
        animation: ripple-animation 0.6s linear;
        pointer-events: none;
      }
      
      @keyframes ripple-animation {
        to {
          transform: scale(4);
          opacity: 0;
        }
      }
      
      .scroll-indicator {
        position: fixed;
        top: 0;
        left: 0;
        width: 0%;
        height: 3px;
        background: var(--primary-color);
        z-index: 9999;
        transition: width 0.3s ease;
      }
      
      .animate-spin {
        animation: spin 1s linear infinite;
      }
      
      @keyframes spin {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
      }
      
      .lazy {
        opacity: 0;
        transition: opacity 0.3s;
      }
      
      .lazy.loaded {
        opacity: 1;
      }
    `;
    document.head.appendChild(style);

    // Add scroll indicator to body
    document.body.insertAdjacentHTML('afterbegin', '<div class="scroll-indicator"></div>');
  </script>
    
  </body>
</html> 