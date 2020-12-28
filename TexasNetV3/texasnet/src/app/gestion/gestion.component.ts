import { Component, OnInit, OnDestroy, Input } from '@angular/core';
import { CdkDragDrop, CdkDragEnter, CdkDragEnd, moveItemInArray } from '@angular/cdk/drag-drop';
import { ProduitService } from '../services/produits.service';
import { Produits } from '../models/produits.model';
import { Subscription } from 'rxjs';
import { HttpClient } from '@angular/common/http';
import { HttpRequest } from '../services/http-request.service';
import { ImageService } from '../services/images.service';
import { ModuleService } from '../services/modules.service';
import { DetailService } from '../services/detail-produit.service';
import { Menu } from '../models/menu.models';
import { MenuService } from '../services/menu.service';
import { MatSnackBar } from '@angular/material';

@Component({
  selector: 'app-gestion',
  templateUrl: './gestion.component.html',
  styleUrls: ['./gestion.component.css']
})
export class GestionComponent implements OnInit, OnDestroy {
  produits: Produits[];
  allProduits: Produits[];
  produitModal: Produits;
  produitModalColori: any;
  produitModalSelected: boolean;
  produitSubscription: Subscription;
  prixVenteConseille: boolean; //statut du prix de vente conseille
  libelleProduit: string; //libelle du produit à afficher dans le modal pour affecter un prix aux tailles d'un produit
  codeColoris: string;
  libColoris: string;
  tailleProduitPromo: any[] = [];
  tarifProduitPromo: any[] = [];
  valuePromo: any[] = [];
  bPromoChecked: boolean[] = [];
  fPromoValue: number[] = [];
  lastFrom: number = -1;
  lastTo: number = -1;
  nbActifSubMenu: number;
  menuNavbar: any[] = [];
  ordreParam: any[] = [];
  currentSubmenu: number = 1;
  myAnyParam: any[] = [];
  booting: number=0;
  MenuArrayDoubleSubscription:Subscription;
  updatingState = false;

  constructor(private detailService: DetailService, private moduleService: ModuleService, private produitService: ProduitService, private httpClient: HttpClient, private httpRequest: HttpRequest, private imageService: ImageService, private snackBar: MatSnackBar, private menuService:MenuService) {
    this.produitService.recupProduitGestion();

  }
  @Input() recherche: string;
  @Input() cocher: string;

  ngOnInit() {

      // Initialisation menu
      this.MenuArrayDoubleSubscription=this.menuService.menuSubject.subscribe(
        (menu:any[])=>{
          this.menuNavbar = menu;
        });
      this.menuService.initialiseMenu();
      this.menuService.appelMenu();

      // Initialisation produit
    this.produitSubscription = this.produitService.produitSubjectGestion.subscribe(
      (produit: Produits[]) => {
        this.updatingState = false;
        this.produits = JSON.parse(JSON.stringify(produit));
        // Deep clone
        this.allProduits = JSON.parse(JSON.stringify(this.produits));
        if (this.booting >=2) {
            this.currentSubmenu < 0 ? this.filtreSelonMenu(this.currentSubmenu, this.myAnyParam) : this.filtreSelonMenu(this.currentSubmenu-1, this.myAnyParam);
        }
        this.booting++;
      }
    );
    this.produitService.emitProduitsGestion();

    this.httpClient.post(this.httpRequest.MenuInfo, {
      "choix": "activeSubMenu"
    }).subscribe(data => {
      this.nbActifSubMenu = data["nbActif"];
    });

    this.httpClient.post(this.httpRequest.MenuInfo, {
      "choix": "info"
    }).subscribe(data => {
      this.ordreParam = [];
      let index = 0;
      let comparator = 0;
      for (let i = 1; i <= 7; i++) {
        for (let param of Object.values(data)) {
          if (Number(param.ordreMenu) === i && Number(param.actif) === 1) {
            this.ordreParam.push(param.nom);
            break;
          }
        }
      }
    })

    this.moduleService.infoModules().then(data => {
      if (data["prixVenteConseille"] == 1) {
        this.prixVenteConseille = true;
      } else {
        this.prixVenteConseille = false;
      }
    });
  }

  filtreSelonMenu(indexTraitement: number = 0, paramsValue: any[] = []) {
    if (paramsValue[0] === "Promo") {
      indexTraitement*=-1;
      paramsValue.shift();
    }
    this.myAnyParam = paramsValue;
    indexTraitement >= 0 ? this.currentSubmenu = indexTraitement + 1 : this.currentSubmenu = indexTraitement;
    let indexsToRemove = []
    // Deep clone
    this.produits = JSON.parse(JSON.stringify(this.allProduits));
    // On cherche les indexs à filtrer
    switch (indexTraitement) {
      case 0:
        // traitement effectué au dessus
        break;

      case 1:
        for (let i = 0; i < this.allProduits.length; i++) {
          if (this.allProduits[i][this.ordreParam[0]] !== paramsValue[0]) {
            indexsToRemove.push(i);
          }
        }
        break;

      case 2:
        for (let i = 0; i < this.allProduits.length; i++) {
          if (this.allProduits[i][this.ordreParam[0]] !== paramsValue[0] ||
            this.allProduits[i][this.ordreParam[1]] !== paramsValue[1]) {
            indexsToRemove.push(i);
          }
        }
        break;

      case 3:
        for (let i = 0; i < this.allProduits.length; i++) {
          if (this.allProduits[i][this.ordreParam[0]] !== paramsValue[0] ||
            this.allProduits[i][this.ordreParam[1]] !== paramsValue[1] ||
            this.allProduits[i][this.ordreParam[2]] !== paramsValue[2]) {
            indexsToRemove.push(i);
          }
        }
        break;

      case 4:
        for (let i = 0; i < this.allProduits.length; i++) {
          if (this.allProduits[i][this.ordreParam[0]] !== paramsValue[0] ||
            this.allProduits[i][this.ordreParam[1]] !== paramsValue[1] ||
            this.allProduits[i][this.ordreParam[2]] !== paramsValue[2] ||
            this.allProduits[i][this.ordreParam[3]] !== paramsValue[3]) {
            indexsToRemove.push(i);
          }
        }
        break;

      case 5:
        for (let i = 0; i < this.allProduits.length; i++) {
          if (this.allProduits[i][this.ordreParam[0]] !== paramsValue[0] ||
            this.allProduits[i][this.ordreParam[1]] !== paramsValue[1] ||
            this.allProduits[i][this.ordreParam[2]] !== paramsValue[2] ||
            this.allProduits[i][this.ordreParam[3]] !== paramsValue[3] ||
            this.allProduits[i][this.ordreParam[4]] !== paramsValue[4]) {
            indexsToRemove.push(i);
          }
        }
        break;

      case 6:
        for (let i = 0; i < this.allProduits.length; i++) {
          if (this.allProduits[i][this.ordreParam[0]] !== paramsValue[0] ||
            this.allProduits[i][this.ordreParam[1]] !== paramsValue[1] ||
            this.allProduits[i][this.ordreParam[2]] !== paramsValue[2] ||
            this.allProduits[i][this.ordreParam[3]] !== paramsValue[3] ||
            this.allProduits[i][this.ordreParam[4]] !== paramsValue[4] ||
            this.allProduits[i][this.ordreParam[5]] !== paramsValue[5]) {
            indexsToRemove.push(i);
          }
        }
        break;

      case -1:
        for (let i = 0; i < this.allProduits.length; i++) {
          if (String(this.allProduits[i].promo) !== '1') {
            indexsToRemove.push(i);
          }
        }
        break;

      case -2:
        for (let i = 0; i < this.allProduits.length; i++) {
          if (this.allProduits[i][this.ordreParam[0]] !== paramsValue[0] || String(this.allProduits[i].promo) !== '1') {
            indexsToRemove.push(i);
          }
        }
        break;

      case -3:
        for (let i = 0; i < this.allProduits.length; i++) {
          if (this.allProduits[i][this.ordreParam[0]] !== paramsValue[0] ||
            this.allProduits[i][this.ordreParam[1]] !== paramsValue[1] ||
            String(this.allProduits[i].promo) !== '1') {
            indexsToRemove.push(i);
          }
        }
        break;

      case -4:
        for (let i = 0; i < this.allProduits.length; i++) {
          if (this.allProduits[i][this.ordreParam[0]] !== paramsValue[0] ||
            this.allProduits[i][this.ordreParam[1]] !== paramsValue[1] ||
            this.allProduits[i][this.ordreParam[2]] !== paramsValue[2] ||
            String(this.allProduits[i].promo) !== '1') {
            indexsToRemove.push(i);
          }
        }
        break;

      case -5:
        for (let i = 0; i < this.allProduits.length; i++) {
          if (this.allProduits[i][this.ordreParam[0]] !== paramsValue[0] ||
            this.allProduits[i][this.ordreParam[1]] !== paramsValue[1] ||
            this.allProduits[i][this.ordreParam[2]] !== paramsValue[2] ||
            this.allProduits[i][this.ordreParam[3]] !== paramsValue[3] ||
            String(this.allProduits[i].promo) !== '1') {
            indexsToRemove.push(i);
          }
        }
        break;

      case -6:
        for (let i = 0; i < this.allProduits.length; i++) {
          if (this.allProduits[i][this.ordreParam[0]] !== paramsValue[0] ||
            this.allProduits[i][this.ordreParam[1]] !== paramsValue[1] ||
            this.allProduits[i][this.ordreParam[2]] !== paramsValue[2] ||
            this.allProduits[i][this.ordreParam[3]] !== paramsValue[3] ||
            this.allProduits[i][this.ordreParam[4]] !== paramsValue[4] ||
            String(this.allProduits[i].promo) !== '1') {
            indexsToRemove.push(i);
          }
        }
        break;

      case -7:
        for (let i = 0; i < this.allProduits.length; i++) {
          if (this.allProduits[i][this.ordreParam[0]] !== paramsValue[0] ||
            this.allProduits[i][this.ordreParam[1]] !== paramsValue[1] ||
            this.allProduits[i][this.ordreParam[2]] !== paramsValue[2] ||
            this.allProduits[i][this.ordreParam[3]] !== paramsValue[3] ||
            this.allProduits[i][this.ordreParam[4]] !== paramsValue[4] ||
            this.allProduits[i][this.ordreParam[5]] !== paramsValue[5] ||
            String(this.allProduits[i].promo) !== '1') {
            indexsToRemove.push(i);
          }
        }
        break;
    }

    // On filtre le tableau d'affichage
    for (let i = 0; i < indexsToRemove.length; i++) {
      this.produits.splice(indexsToRemove[i] - i, 1); // On retire i à l'index à supprimer car splice modifie l'index
    }

    if (this.produits.length === 0) {
      this.snackBar.open("Aucun produit ne correspond aux critères séléctionnés.", "X", {
        duration: 3000,
      });
    } else {
      this.produits.sort((a, b) => a.position[this.currentSubmenu] - b.position[this.currentSubmenu]);
    }

    for(let produit of this.produits) {
      if(produit.position[this.currentSubmenu] === "99999") {
        this.validPos();
        break;
      }
    }
  }

  onValidChangePos(event, moveFrom: number) {
    let tailleP1 = 1;
    let tailleP2 = 1;
    let refP1 = '';
    let refP2 = '';
    if (event.key === "Enter") {
      // Récupère la valeur de l'input
      let moveTo: number = Number(event.srcElement.value) - 1;
      if (moveTo >= 0 && moveTo < this.produits.length && moveTo !== moveFrom) {
        while (moveFrom > 0 && moveFrom < this.produits.length && this.produits[moveFrom].refproduit === this.produits[moveFrom - 1].refproduit) {
          moveFrom--;
        }
        while (moveFrom >= 0 && moveFrom + tailleP1 < this.produits.length && this.produits[moveFrom].refproduit === this.produits[moveFrom + tailleP1].refproduit) {
          tailleP1++;
        }
        refP1 = this.produits[moveFrom].refproduit;
        while (moveTo > 0 && moveTo < this.produits.length && this.produits[moveTo].refproduit === this.produits[moveTo - 1].refproduit) {
          moveTo--;
        }
        while (moveTo >= 0 && moveTo + tailleP2 < this.produits.length && this.produits[moveTo].refproduit === this.produits[moveTo + tailleP2].refproduit) {
          tailleP2++;
        }
        refP2 = this.produits[moveTo].refproduit;

        if (moveFrom > moveTo) {
          let tmp: any;
          //-----------//
          tmp = moveFrom;
          moveFrom = moveTo;
          moveTo = tmp;
          //-----------//
          tmp = tailleP1;
          tailleP1 = tailleP2;
          tailleP2 = tmp;
          //-----------//
          tmp = refP1;
          refP1 = refP2;
          refP2 = tmp;
        }
        this.updatingState = true;
        // Echange les valeurs
        this.httpClient.post(this.httpRequest.GestionProduits, {
          "login": sessionStorage.getItem("loginCompte"),
          "type": 'exchange',
          "nummenu": this.currentSubmenu,
          "refproduit1": refP1,
          "refproduit2": refP2,
          "indexFrom": moveFrom,
          "indexTo": moveTo,
          "tailleP1": tailleP1,
          "tailleP2": tailleP2
        }).subscribe(data => {
          this.produitService.recupProduitGestion();
        });
      }
    }
  }

  enterDrag(event: CdkDragEnter) {
    if (this.produits.length < 2) {
      return;
    }
    this.lastFrom = event.item.data;
    this.lastTo = event.container.data;
  }

  drop(event: CdkDragEnd) {
    if (this.lastFrom === -1 || this.lastTo === -1 || this.produits.length < 2 || this.lastFrom === this.lastTo) {
      return;
    }
    let direction = 0;
    let amplitude = 1;

    this.lastTo > this.lastFrom ? direction = 1 : direction = -1;

    while (this.lastTo + direction < this.produits.length && this.lastTo + direction >= 0 && this.produits[this.lastTo].refproduit === this.produits[this.lastTo + direction].refproduit) {
      this.lastTo += direction;
    }
    while (this.lastFrom > 0 && this.produits[this.lastFrom].refproduit === this.produits[this.lastFrom - 1].refproduit) {
      this.lastFrom--;
    }
    while (this.lastFrom + amplitude < this.produits.length && this.produits[this.lastFrom].refproduit === this.produits[this.lastFrom + amplitude].refproduit) {
      amplitude++;
    }

    this.updatingState = true;
    this.httpClient.post(this.httpRequest.GestionProduits, {
      "login": sessionStorage.getItem("loginCompte"),
      "type": "drop",
      "nummenu": this.currentSubmenu,
      "refproduitC1": this.produits[this.lastFrom].refproduit,
      "refproduitC2": this.produits[this.lastTo].refproduit,
      "indexFrom": this.lastFrom + 1,
      "indexTo": this.lastTo + 1,
      "amplitude": amplitude
    }).subscribe(data => {
      this.produitService.recupProduitGestion();
    });
    this.lastFrom = -1;
    this.lastTo = -1;
  }

  /* Gestion de la selection du moment */
  changeSelection(value) {
    this.updatingState = true;
    let selectToUpdate:number;
    this.produitModalSelected ? selectToUpdate = 0 : selectToUpdate = 1;
    this.httpClient.post(this.httpRequest.UpdateGestionProduits, {
      "choix": "select",
      "refproduit": this.produitModal.refproduit,
      "codeColori": this.codeColoris,
      "selectToUpdate": selectToUpdate
    }).subscribe(data => {
      this.produitService.recupProduitGestion();
      this.updatingState = false;
    });
  }

  /* Gestion des promos */
  changePromo(value, idProduit, p) {
    if (value.checked === false) {
      this.updatingState = true;
      this.bPromoChecked[p] = false;
      this.httpClient.post(this.httpRequest.UpdateGestionProduits, {
        "choix": "promo",
        "idProduit": idProduit,
        "promoToUpdate": 0
      }).subscribe(data => {
        this.menuService.initialiseMenu();
        this.produitService.recupProduitGestion();
      });
    } else {
      this.bPromoChecked[p] = true;
    }
  }

  /* Gestion tarif promo */
  changeTarifPromo(event, idProduit, ancienPrix) {
    let prix = Number(event.target.value);
    if (Number(prix) > 0) {
      this.httpClient.post(this.httpRequest.UpdateGestionProduits, {
        "choix": "promo",
        "idProduit": idProduit,
        "promoToUpdate": 1
      }).subscribe(data => {
        this.httpClient.post(this.httpRequest.UpdateGestionProduits, {
          "choix": "tarifPromo",
          "tarifToUpdate": Number(prix),
          "idProduit": idProduit
        }).subscribe(data => {
          this.menuService.initialiseMenu();
          this.produitService.recupProduitGestion();
        });
      });
    } else {
      event.target.value = Number(ancienPrix);
      this.snackBar.open("Veuillez entrer un montant valide.", "X", {
        duration: 3000,
      });
    }
  }

  /* Gestion du tarif_pvc */
  changeTarifPVC(event: number, idproduit) {
    this.httpClient.post(this.httpRequest.UpdateGestionProduits, {
      "choix": "tarif_pvc",
      "tarifToUpdate": Number(event),
      "idProduit": idproduit
    }).subscribe(data => {
      this.produitService.recupProduitGestion();
    })
  }

  gestionTaillesPromo(refproduit, libelle) {
    this.tailleProduitPromo = [];
    this.libelleProduit = libelle;
    this.detailService.getDetail(refproduit).then(data => {
      for (let i = 0; i < data[4][0].length; i++) {
        this.tailleProduitPromo[i] = { "taille": data[4][0][i].taille, "tarif_promoL": data[4][0][i].tarif_promoL, "idDetailProduit": data[4][0][i].idproduit }
      }
    });
  }

  gestionTarifsPromo(produit, coloris, indexColori) {
    this.tailleProduitPromo = [];
    this.tarifProduitPromo = [];
    this.produitModal = produit;
    this.produitModalColori = produit.arrayColori[indexColori];
    this.libColoris = coloris;
    this.codeColoris = this.produitModalColori.codeColori
    Number(this.produitModalColori.selection) === 1 ? this.produitModalSelected = true : this.produitModalSelected = false;

    for (let i = 0; i < this.produits.length; i++) {
      if (this.produits[i] === this.produitModal) {
        for (let unArrayTarif of this.produits[i].arrayTarif) {
          if (unArrayTarif.libcolori === coloris) {
            this.tarifProduitPromo.push(unArrayTarif);
          }
        }
        for (let y = 0; y < this.tarifProduitPromo.length; y++) {
          let bChecked = true;
          if (this.tarifProduitPromo[y].promo === '0') {
            bChecked = false;
          }
          this.bPromoChecked[y] = bChecked;
          this.fPromoValue[y] = this.tarifProduitPromo[y].tarif_promo;
        }
      }
    }
  }

  /* Gestion du tarif_promoL */
  changeTarifPromoL(event: number, idproduit) {
    this.httpClient.post(this.httpRequest.UpdateGestionProduits, {
      "choix": "tarif_promoL",
      "tarifToUpdate": Number(event),
      "idProduit": idproduit
    }).subscribe()
  }

  validPos() {
    this.updatingState = true;
    let tmpRefprodList = [];
    for (let unprod of this.produits) {
      tmpRefprodList.push(unprod.refproduit);
    }
    this.httpClient.post(this.httpRequest.GestionProduits, {
      "login": sessionStorage.getItem("loginCompte"),
      "type": 'set',
      "nummenu": this.currentSubmenu,
      "refprodarray": tmpRefprodList
    }).subscribe(data => {
      this.updatingState = false;
    });
  }

  ngOnDestroy() {
    this.produitSubscription.unsubscribe();
  }

}
