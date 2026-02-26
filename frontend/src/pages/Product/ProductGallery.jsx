
import { useState, useEffect } from "react";
import { Fancybox } from "@fancyapps/ui";
import "@fancyapps/ui/dist/fancybox/fancybox.css";
import Slider from "react-slick";


function ProductGallery({ images }) {

    const [nav1, setNav1] = useState(null);
    const [nav2, setNav2] = useState(null);

    useEffect(() => {
      Fancybox.bind("[data-fancybox='gallery']", {})
    
      return () => {
        Fancybox.destroy()
      }
    }, [])
    return (
      <div className="product-gallery">
  
        {/* ẢNH LỚN */}
        <Slider
        asNavFor={nav2}
        ref={(slider) => setNav1(slider)}
        arrows={false}
        fade={true}          // ⭐ FADE EFFECT
        speed={500}
        >
          {images.map((img, i) => (
            <div key={i}>
               <a
              href={img}
              data-fancybox="gallery"
              data-caption={`Image ${i + 1}`}
              >
              <img src={img} alt="" className="main-img" /></a>
            </div>
          ))}
        </Slider>
  
        {/* THUMBNAIL */}
        <Slider
        asNavFor={nav1}
        ref={(slider) => setNav2(slider)}
        slidesToShow={4}
        swipeToSlide
        focusOnSelect
        arrows
        className="thumb-slider"
        >
          {images.map((img, i) => (
            <div key={i}>
              <img src={img} alt="" className="thumb-img" />
            </div>
          ))}
        </Slider>
  
      </div>
    );
  }
  
  export default ProductGallery;