import { Component, OnInit } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { HttpRequest } from '../../services/http-request.service';
import { AuthService } from '../../services/auth.service';
import { Router } from '@angular/router';
import { ProduitService } from 'src/app/services/produits.service';

@Component({
  selector: 'app-contenu-representant',
  templateUrl: './contenu-representant.component.html',
  styleUrls: ['./contenu-representant.component.css']
})
export class ContenuRepresentantComponent implements OnInit {
  clientRep: any[] = []; // source of truth
  afficheClient: any[] = [];
  societeRep: any[] = [];
  codeClientRep: any[] = [];
  infoClientRep: any[] = [];
  fonctionRep: any[] = [];
  loginRep: string;
  recherche: string = '';

  constructor(private produitService: ProduitService, private httpRequest: HttpRequest, private httpClient: HttpClient, private authService: AuthService, private router: Router) { }

  ngOnInit() {
    this.loginRep = sessionStorage.getItem("loginRepresentant");
    this.httpClient.post(this.httpRequest.InfoRepresentant, {
      "loginRepresentant": sessionStorage.getItem("loginRepresentant")
    }).subscribe(data => {
      for (let i = 0; i < (data[1].loginClient).length; i++) {
        this.clientRep[i] = data[1].loginClient[i];
        this.afficheClient[i] = true;
        this.societeRep[i] = data[2][i]["infoClient"].raisonSociale + " " + data[2][i]["infoClient"].complementLivraison;
        this.codeClientRep[i] = data[2][i]["infoClient"].codeClient;
        //this.fonctionRep[i] = data[2][i]["infoClient"].fonction;
        if (i === 0)
          sessionStorage.setItem("loginCompte", this.clientRep[i]);
      }
      this.infoClientRep = data[2];
    })
  }

  /* Connexion à un compte client depuis un représentant */
  getClientRep(login, id) {
    this.authService.isAuth = true;
    sessionStorage.setItem("isLoggedIn", "true");
    sessionStorage.setItem('loginCompte', login);
    sessionStorage.setItem("infoClient", JSON.stringify(this.infoClientRep[id].infoClient));
    sessionStorage.setItem("logWithRepAcc", "true");
    this.authService.loginCompte = sessionStorage.getItem('loginCompte');
    this.router.navigate(['/contenu/accueil']);
  }

  filterList(input) {
    input = input.toLowerCase();
    for(let i = 0; i < this.clientRep.length; i++) {
      let temp = this.clientRep[i].toLowerCase().indexOf(input);
      let temp2 = this.societeRep[i].toLowerCase().indexOf(input);
      if (temp  !== -1 || temp2 !== -1) {
        this.afficheClient[i] = true;
      } else {
        this.afficheClient[i] = false;
      }
    }
  }

}
