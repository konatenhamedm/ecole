<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>
    <div class="form-group">
        <label for="prix">Prix</label>
        <input type="text" id="prix" value="{{ data.prix }}" class="form-control">
    </div>
    <div class="container my-5">
        <form action="" class="form" method="" action="">
            <button type="submit">Confirmer</button>
        </form>
    </div>

    <img src="{{ asset('image/success.jpg') }}" alt="" class="w-100">
    <h3 class="text-center">Une erreur est survenue !</h3>
    <h4 class="text-center">{{ data.msg }}</h4>
    <a href="route" class="btn theme_btn button_hover">Retour aux réservations</a>
</body>
</html>