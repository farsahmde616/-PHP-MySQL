let profile = document.querySelector('.header .flex .profile');
let navbar = document.querySelector('.header .flex .navbar');

// تفعيل أو إلغاء تفعيل واجهة المستخدم الشخصية عند النقر على زر المستخدم
document.querySelector('#user-btn').onclick = () => {
   profile.classList.toggle('active'); // تبديل الحالة النشطة
   navbar.classList.remove('active'); // إلغاء تفعيل شريط التنقل
}

// تفعيل أو إلغاء تفعيل شريط التنقل عند النقر على زر القائمة
document.querySelector('#menu-btn').onclick = () => {
   navbar.classList.toggle('active'); // تبديل الحالة النشطة
   profile.classList.remove('active'); // إلغاء تفعيل واجهة المستخدم الشخصية
}

// إلغاء تفعيل واجهة المستخدم الشخصية وشريط التنقل عند التمرير
window.onscroll = () => {
   profile.classList.remove('active'); // إلغاء تفعيل واجهة المستخدم الشخصية
   navbar.classList.remove('active'); // إلغاء تفعيل شريط التنقل
}

// تغيير الصورة الرئيسية عند النقر على الصور الفرعية
let subImages = document.querySelectorAll('.update-product .image-container .sub-images img');
let mainImage = document.querySelector('.update-product .image-container .main-image img');

subImages.forEach(images => {
   images.onclick = () => {
      let src = images.getAttribute('src'); // الحصول على مصدر الصورة الفرعية
      mainImage.src = src; // تعيين مصدر الصورة الرئيسية إلى المصدر الجديد
   }
});