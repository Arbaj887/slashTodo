<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com?plugins=forms,typography,aspect-ratio,line-clamp,container-queries"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.0/css/all.min.css" integrity="sha512-9xKTRVabjVeZmc+GUW8GgSmcREDunMM+Dt/GrzchfN8tkwHizc5RP4Ok/MXFFy5rIjJjzhndFScTceq5e6GvVQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <title>Document</title>
</head>
<body>
    <?php if(session()->getFlashdata('popMessage')): ?>
    <div class="absolute right-0 top-0  bg-white shadow-xl rounded-lg  m-5 min-w-[250px] animate-bounce " id="showError">
            <div class="w-full flex justify-end p-2  m-1 px-3 ">
            <i class=" fa-regular fa-circle-xmark hover:text-red-700 pr-3" 
            
            onclick="hideMessage()"></i>
            </div>
            <div class="p-4">
            
            <h1 class="text-pretty px-3"><?php echo session()->getFlashdata('popMessage');?></h1>
               
            </div>
            
        
    </div>
    <?php endif; ?>
</body>
<script>
    setTimeout(()=>{
        hideMessage()
       

    },8000)
    function hideMessage(){
        
        document.getElementById('showError').style.display="none";
      
    }
    

</script>
</html>