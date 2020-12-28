import { Component, OnInit } from '@angular/core';
import { Router } from '@angular/router'
import { BreakpointObserver } from '@angular/cdk/layout';
import { ProduitService } from '../services/produits.service';
import { HttpClient } from '@angular/common/http';
import { HttpRequest } from '../services/http-request.service';
import { MatSnackBar } from '@angular/material';
import { Subscription } from 'rxjs';
import { TemplateService } from '../services/template.service';
import { ActivatedRoute } from '@angular/router';

@Component({
  selector: 'app-approuveur',
  templateUrl: './approuveur.component.html',
  styleUrls: ['./approuveur.component.css']
})
export class ApprouveurComponent implements OnInit {
  isDesktop: boolean;
  isMobile: boolean;
  isTablet: boolean;
  tailleEcran: number = 1;
  loginApprouveur: string = "";
  listeCommandes: any[] = [];  // Regroupe la liste des commandes
  listeCommandesHisto: any[] = [];  // Regroupe la liste des commandes
  modalCmdId: number = 0;
  currentDetails: any[] = [];  // Regroupe le détail de chaque commande des commandes à approuver
  currentDetailsHisto: any[] = [];  // Regroupe le détail de chaque commande des commandes de l'historiques
  selected: boolean[] = [];  // Permet de voir si la ligne à approuver est séléctionné
  selectedHisto: boolean[] = [];  // Permet de voir si la ligne de l'historique est séléctionné
  previousIndex: number = -1;
  previousIndexHisto: number = -1;
  contenuColor: string;
  contenuColorSubscription: Subscription;
  commandesExiste: boolean;
  motifRefus: string = '';
  modeAffichage: string = '';

  constructor(private router: Router, private breakPoint: BreakpointObserver, private produitService: ProduitService, private httpRequest: HttpRequest, private httpClient: HttpClient, private snackBar: MatSnackBar, private templateService: TemplateService, private route:ActivatedRoute) {

    breakPoint.observe([
      '(max-width: 1288px)'
    ]).subscribe(result => {
      if (result.matches) {
        this.tailleEcran = 2; //si atteint 1288px passe la taille à 2 --> 3 produits
      } else {
        this.tailleEcran = 1; //sinon revient à une taille à 1 --> 4 produits
        var modules = document.getElementById("modules");
        modules.style.position = "relative";
        modules.style.marginRight = "2.5%";
        modules.style.marginLeft = "0%";
        modules.style.marginTop = "0px";
      }
    });
    breakPoint.observe([
      '(max-width: 785px)'
    ]).subscribe(result => {
      if (result.matches) {
        this.tailleEcran = 3; //si atteint 785px passe la taille à 3 --> 2 produits
      } else {
        if (Number(window.innerWidth) < 1288) {
          this.tailleEcran = 2; //sinon si la taille est inférieure à 1288px revient à une taille à 2 --> 3 produits
        }
      }
    });
    breakPoint.observe([
      '(max-width: 550px)'
    ]).subscribe(result => {
      if (result.matches) {
        this.tailleEcran = 4; //si atteint 550 px passe la taille à 4 --> 1 produit
      } else {
        if (Number(window.innerWidth) < 785) {
          this.tailleEcran = 3 //sinon si la taille est inférieure à 785px revient à une taille à 3 --> 2 produits
        }
      }
    });
    breakPoint.observe([
      '(max-width: 1000px)'
    ]).subscribe(result => {
      if (result.matches) {
        this.appModdules(); //lorsqu'on atteint une taille < à 1000px affiche les modules en bas du contenu
      }
    })
  }

  ngOnInit() {
      // Récupère la valeur du parametre pour changer d'affichage
    this.route.params.subscribe(
      (value)=>{
        this.modeAffichage = value["type"];
      });

    if (sessionStorage.getItem("approuveur") !== 'true') {
      this.router.navigate(['']);
      sessionStorage.clear();
    }

    this.templateService.getContenuColor();
    this.contenuColorSubscription = this.templateService.contenuColorSubject.subscribe(
      (contenuColor: string) => {
        this.contenuColor = '#' + contenuColor;
      }
    );
    this.templateService.emitContenuColor();

    this.isDesktop = this.produitService.isDesktop; //renvoi true si Desktop
    this.isMobile = this.produitService.isMobile; //renvoi true si Mobile
    this.isTablet = this.produitService.isTablet; //renvoi true si tablet

    this.loginApprouveur = sessionStorage.getItem("loginCompte");
    this.httpClient.post(this.httpRequest.InfoApprouveur, {
      "loginApprouveur": this.loginApprouveur
    }).subscribe(data => {
      this.listeCommandes = [];
      this.listeCommandesHisto = [];
      this.commandesExiste = data[0];
      if (data[0] === true) {
          // liste commandes de l'historique
        this.listeCommandesHisto = data[1];
        for(let uneCommandeHisto of this.listeCommandesHisto) {
          this.selectedHisto.push(false);
        }

          // liste commandes à approuver
        for (let i = 0; i < data[1].length; i++) {
          if (data[1][i].etat === "1" || data[1][i].etat === "A valider") {
            this.listeCommandes.push(data[1][i]);
          }
        }
        for (let uneCommande of this.listeCommandes) {
          this.selected.push(false);
        }
      }
    });
  }

  updateOneCmd(numCommande: number, typeTraitement: string) {
    let tabApprouveCom = [];
    if (typeTraitement == 'approuve') {
      tabApprouveCom.push([numCommande, "En attente", ""]);
    } else if (typeTraitement == 'refuse') {
      tabApprouveCom.push([numCommande, "Refuse", this.motifRefus]);
    }

    let indexToSplice = -1;
    for (let i = 0; i < this.listeCommandes.length; i++) {
      if (typeof(this.listeCommandes[i].numCommande) !== 'undefined' && this.listeCommandes[i].numCommande == numCommande) {
        indexToSplice = i;
      }
    }
    if (indexToSplice !== -1) {
      this.listeCommandes.splice(indexToSplice, 1);
    }

    this.httpClient.post(this.httpRequest.UpdateEtatCommande, {
      "tabApprouveCom": tabApprouveCom
    }).subscribe();


    if (typeTraitement == 'approuve') {
      this.snackBar.open("La commande " + numCommande + " a été approuvée.", "", {
        duration: 3000
      })
    } else if (typeTraitement == 'refuse') {
      this.snackBar.open("La commande " + numCommande + " a été refusée.", "", {
        duration: 3000
      })
    }
    this.motifRefus = '';
    this.ngOnInit();
  }

  approveAll() {
    let tabApprouveCom = [];
    for (let i = 0; i < this.listeCommandes.length; i++) {
      if (typeof (this.listeCommandes[i]) !== 'undefined') {
        tabApprouveCom.push([this.listeCommandes[i].numCommande, "En attente"], "");
      }
    }
    this.httpClient.post(this.httpRequest.UpdateEtatCommande, {
      "tabApprouveCom": tabApprouveCom
    }).subscribe();

    this.snackBar.open("Toutes les commandes ont été approuvées.", "", {
      duration: 3000
    });
    this.listeCommandes = [];
    this.ngOnInit();
  }

  afficherDetails(numCommande: number, index: number) {
    if (this.modeAffichage === 'liste') {
        // Si on clique sur un nouvelle index, on affiche les détails de la commande
      if (index !== this.previousIndex) {
        this.selected[this.previousIndex] = false;
        this.previousIndex = index;
        this.selected[index] = true;
        this.httpClient.post(this.httpRequest.InfoDetailsCommande, {
          "numCommande": numCommande
        }).subscribe(data => {
          if (data[0] === true) {
            this.currentDetails = data[1];
          } else {
            this.snackBar.open("Une erreur est survenue lors de l'affichage des détails de la commande.", "", {
              duration: 2500
            });
          }
        });
        // Sinon on ferme les détails affichés
      } else {
        this.previousIndex = -1;
        this.selected[index] = false;
        this.currentDetails = [];
      }
    } else if (this.modeAffichage === 'history') {
        // Si on clique sur un nouvelle index, on affiche les détails de la commande
        if (index !== this.previousIndexHisto) {
          this.selectedHisto[this.previousIndexHisto] = false;
          this.previousIndexHisto = index;
          this.selectedHisto[index] = true;
          this.httpClient.post(this.httpRequest.InfoDetailsCommande, {
            "numCommande": numCommande
          }).subscribe(data => {
            if (data[0] === true) {
              this.currentDetailsHisto = data[1];
            } else {
              this.snackBar.open("Une erreur est survenue lors de l'affichage des détails de la commande.", "", {
                duration: 2500
              });
            }
          });
          // Sinon on ferme les détails affichés
        } else {
          this.previousIndexHisto = -1;
          this.selectedHisto[index] = false;
          this.currentDetailsHisto = [];
        }
    }
  }

  updateQte(index) {
    let inputFields = document.getElementsByName("newQteR");
    let quantite = inputFields['0'].value;
    //var quantiteM = this.currentDetails[0].quantite - quantite;
    var quantiteM = this.currentDetails[index].quantite - quantite;

    this.httpClient.post(this.httpRequest.UpdatePanier, {
      "login": sessionStorage.getItem("loginCompte"),
      "idproduit": this.currentDetails[0].idDetailProduit,
      "action": "minus",
      "quantite": quantiteM,
      "app": "approuveur",
      "numCommande": this.currentDetails[0].numCommande
    }).subscribe(data => {
      alert('ok');
    })

  }


  appModdules() { //permet d'afficher les modules en bas du contenu lorsque la largeur de la page est inférieure à 1000px
    var modules = document.getElementById("modules");
    modules.style.height = "auto";
    modules.style.marginLeft = "2.5%";
    modules.style.marginRight = "2.5%";
    modules.style.position = "relative";
    modules.style.marginTop = "5%";
  }

}
