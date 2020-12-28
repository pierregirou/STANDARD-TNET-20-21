import { Component, OnInit } from '@angular/core';
import { Router } from '@angular/router';
@Component({
  selector: 'app-four-oh-four',
  templateUrl: './four-oh-four.component.html',
  styleUrls: ['./four-oh-four.component.css']
})
export class FourOhFourComponent implements OnInit {
  comeback:string; //variable de type string qui va indiquer à l'utilisateur si il revient à l'accueil ou à la connexion
  constructor(private router: Router) {
    //sessionStorage.getItem('isLoggedIn')==='true' ? this.comeback="l'accueil" : this.comeback="la connexion"
  }

  ngOnInit() {
    setTimeout(() => {
      this.return();
  }, 10000);  //10s
  }

  return(){
    if(sessionStorage.getItem('isLoggedIn')==='true'){
      this.router.navigate(['/contenu/accueil']); //si connecté revient à l'accueil
    }else{
      this.router.navigate(['/connexion']); //si déconnecté revient à la connexion
    }
  }
}
