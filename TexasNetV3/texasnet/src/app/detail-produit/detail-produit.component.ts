import { Component, OnInit } from '@angular/core';
import { CommandeService } from '../services/commandes.service';
import { ActivatedRoute } from '@angular/router';
import { Produits } from '../models/produits.model'
import { ProduitService } from '../services/produits.service';
import { DetailService } from '../services/detail-produit.service';
import { Detail } from '../models/detailP.models';
import { NgForm } from '@angular/forms';
import { Router } from '@angular/router';
import { ModuleService } from '../services/modules.service';
import { HtmlAstPath } from '@angular/compiler';
import { BreakpointObserver } from '@angular/cdk/layout';
import { MatSnackBar } from '@angular/material/snack-bar';
import { TemplateService } from '../services/template.service';
import { Subscription } from 'rxjs';
import { ImageService } from '../services/images.service';
import { isUndefined } from 'util';
import { LangueService } from '../services/langue.service';
import { FiltreService } from '../services/filtre.service';
import { Location } from '@angular/common'

@Component({
  selector: 'app-detail-produit',
  templateUrl: './detail-produit.component.html',
  styleUrls: ['./detail-produit.component.css']
})
export class DetailProduitComponent implements OnInit {
  produit: Produits;
  refproduit: string;
  saison: string;
  image: string;
  imageZ: string;
  libelle: string;
  libelle2: string;
  marque: string;
  theme: string;
  texteLibre: string;
  champsstat: string;
  textLibre: string;
  famille: string;
  sousFamille: string;
  modele: string;
  colori: string;
  description: HtmlAstPath;
  quantite: number = 0;
  prix: number = 0.00;
  nbColori: number; //permet de connaitre le nombre de colori pour chaque produit
  arrayColori: any[]; //permet de faie une boucle dans le template en fonction du nombre de colori présent
  tailleDisponible: string = " ";
  arrayTaille: any[] = [];
  visInformationTab: string;
  visRefProduit: boolean;
  visTaille: boolean;
  visColoris: boolean;
  visMarque: boolean;
  visTheme: boolean;
  visFamille: boolean;
  visSousFamille: boolean;
  visModele: boolean;
  LibelleConst: string;
  lcLibelle1: boolean;
  lcLibelle2: boolean;
  lcTheme: boolean;
  promo: number;
  PVC: boolean;
  t_pvc: number = 0.00;
  isAuth: boolean;
  contenuColorSubscription: Subscription;
  contenuColor: string;
  codeTarifClient: boolean;
  promoPourcentageCodeTarif: number;
  arrayTailleImage: any[] = [];
  arrayColorisImage: any[] = [];
  arrayColorisLibelle: any[] = [];
  arrayColorisLib: any[] = [];
  arrayTailleImageZ: any[] = [];
  prixSelect: number = 0.00;
  prixSelectPromo: number = 0.00;
  afficherFiltres:boolean;
  langueSelect:any;
  langueSelected:number=1;
  langueSelectSubscription:Subscription;
  codeColoriParam:any;
  libColoriParam:any="";
  afficheSeulementSelection:boolean = false;
  ligne:string;
  colorPourCommandeTaille:any;
  leaving = false;

  afficheInformation: boolean = false;


  constructor(private templateService: TemplateService, private snackBar: MatSnackBar, private commandeService: CommandeService, private route: ActivatedRoute, private produitService: ProduitService, private detailService: DetailService, private router: Router, private moduleService: ModuleService, private imageService: ImageService, private langueService:LangueService, private filtreService:FiltreService, private _Location: Location) {
    this.commandeService.getCommande();
  }

  ngOnInit() {

      this.route.queryParams.subscribe(params => {
        if(typeof(params.selec)!=='undefined') {
          this.afficheSeulementSelection = true;
        }
      });

      // determine si on affiche les filtres
      this.moduleService.getAfficherFiltres().then(
        (data:boolean)=>{
            this.afficherFiltres=data;
        }
      )
    this.codeColoriParam = sessionStorage.getItem('codeColoriDetailProduit');
    this.libColoriParam = sessionStorage.getItem('libColoriDetailProduit');

    if (sessionStorage.getItem("codeTarifClient") === "true") {
      this.codeTarifClient = true;
      this.promoPourcentageCodeTarif = Number(sessionStorage.getItem("promoPourcentageCodeTarif"));
    } else {
      this.codeTarifClient = false;
    }
    this.templateService.getContenuColor();
    this.contenuColorSubscription = this.templateService.contenuColorSubject.subscribe(
      (contenuColor: string) => {
        this.contenuColor = '#' + contenuColor;
      }
    );
    this.templateService.emitContenuColor();
    if (sessionStorage.getItem("isLoggedIn") == "true") {
      this.isAuth = true;
    } else {
      this.isAuth = false;
    }

    const refproduit = this.route.snapshot.params['refproduit']; //récupère le refproduit du produit sélectionné
    /* Récupère en fonction de la référence du produit les détails depuis la méthode getProduit depuis produitService */
      this.produit = this.produitService.getProduit(refproduit);
      this.arrayColori = this.produit.arrayColori;
      this.image = this.produit.image;
      this.imageZ = this.imageService.PhotosArt + this.produit.imageZoom;
      this.saison = this.produit.saison;
      this.refproduit = this.produit.refproduit;
      this.libelle = this.produit.libelle;
      this.libelle2 = this.produit.libelle2;
      this.marque = this.produit.marque;
      this.theme = this.produit.theme;
      this.ligne = this.produit.ligne;
      this.famille = this.produit.famille;
      this.sousFamille = this.produit.sousFamille;
      this.modele = this.produit.modele;
      this.prixSelect = this.produit.prix;
      this.champsstat = this.produit.champsstat;
      this.textLibre = this.produit.texteLibre;
      this.promo = this.route.snapshot.params['prix'];
      this.colori = this.produit.coloris;

      var tab   = [];
      var tabC  = [];
      var tabL  = [];

      for (let f = 0; f < this.produit.arrayColori.length; f++) {
        var prixSelectPromo = this.produit.arrayColori[f].tarif_promo;
        if (!this.afficheSeulementSelection || (this.afficheSeulementSelection && this.produit.arrayColori[f].selection === "1")) {
        if (this.promo === prixSelectPromo) {
          tab.push(this.produit.arrayColori[f].imageMiniature2);
          tabC.push(this.produit.arrayColori[f].codeColori);
          tabL.push(this.produit.arrayColori[f].libcolori);
        } else if(isUndefined(this.promo) && prixSelectPromo === "0.00") {

          tab.push(this.produit.arrayColori[f].imageMiniature2);
          tabC.push(this.produit.arrayColori[f].codeColori);
          tabL.push(this.produit.arrayColori[f].libcolori);
        }
      }
        this.arrayColorisImage = tab;
        this.arrayColorisLib = tabC;
        this.arrayColorisLibelle = tabL;
      }

    for (let i = 0; i < this.produit.arrayColori.length; i++) {
      this.prixSelectPromo = this.produit.arrayColori[i].tarif_promo;
      if (this.promo === this.prixSelectPromo) {
        typeof(this.produit.arrayColori[i].imageMiniature2)==='string'?this.arrayTailleImage=this.produit.arrayColori[i].imageMiniature2.split():this.arrayTailleImage = Object.values(this.produit.arrayColori[i].imageMiniature2);
        this.image = this.imageService.PhotosArt + this.produit.arrayColori[i].image;
        this.imageZ = this.imageService.PhotosArt + this.produit.arrayColori[i].imageZoom;
        i = this.produit.arrayColori.length;
      } else {

        if (this.produit.arrayColori[i].promo !== '1') {

          typeof(this.produit.arrayColori[i].imageMiniature2)==='string'?this.arrayTailleImage=this.produit.arrayColori[i].imageMiniature.split():this.arrayTailleImage = Object.values(this.produit.arrayColori[i].imageMiniature);
          //typeof(this.produit.arrayColori[i].imageMiniature2)==='string'?this.arrayTailleImage=this.produit.arrayColori[i].imageMiniature2.split():this.arrayTailleImage = Object.values(this.produit.arrayColori[i].imageMiniature2); // oakwood
          this.image = this.imageService.PhotosArt + this.produit.arrayColori[i].image;
          this.imageZ = this.imageService.PhotosArt + this.produit.arrayColori[i].imageZoom;
          i = this.produit.arrayColori.length;
        }
      }
    }
    this.changeColoris(this.codeColoriParam);

    this.moduleService.prixVenteConseille().then(data => {
      if (data['prixVenteConseille'] === 1) {
        this.PVC = true;
        //this.t_pvc = this.produitService.getProduit(refproduit).tarifpvc;
      } else {
        this.PVC = false;
      }
    });

    /****************************************************************************************************************** */
    this.detailService.getDetail(this.refproduit).then(
      (data: any[]) => { //récupère les détails du produit
        this.langueSelected=this.langueService.langueSelect;
        this.langueSelectSubscription=this.langueService.langueSelectSubject.subscribe(langue=>{
        this.detailService.getDetail(this.refproduit).then(
          (data2: any[]) => {
            if(data2 && this.refproduit === sessionStorage.getItem('refproduitPourDetailProduit')) {
              this.langueSelect=langue;
              //if(String(langue) === '1')
              this.description = String(langue) === '1' ? data2[5]['FRA'] : data2[5]['ANG'];
              this.marque = data2[4][0][0]['codeMarque'];
              this.theme = data2[4][0][0]['codeTheme'];
              this.famille = data2[4][0][0]['codeFamille'];
              this.ligne = data2[4][0][0]['codeLigne'];
              this.sousFamille = data2[4][0][0]['codeSousFamille'];
              this.modele = data2[4][0][0]['codeModele'];
              this.libelle = String(langue) === '1' ? this.produit.libelle : this.produit.libelleANG;
              if (!this.leaving) {
                this.filtreService.sendCurrentLigne(this.ligne);
                this.filtreService.sendCurrentTheme(this.theme);
                this.filtreService.sendCurrentFamille(this.famille);
              }

              var tab   = [];
              var tabC  = [];
              var tabL  = [];

              for (let f = 0; f < this.produit.arrayColori.length; f++) {
                var prixSelectPromo = this.produit.arrayColori[f].tarif_promo;
                if (!this.afficheSeulementSelection || (this.afficheSeulementSelection && this.produit.arrayColori[f].selection === "1")) {
                  if (this.promo === prixSelectPromo) {
                    tab.push(this.produit.arrayColori[f].imageMiniature2);
                    tabC.push(this.produit.arrayColori[f].codeColori);
                    tabL.push(this.produit.arrayColori[f].libcolori);
                  } else if(isUndefined(this.promo) && prixSelectPromo === "0.00") {
                    tab.push(this.produit.arrayColori[f].imageMiniature2);
                    tabC.push(this.produit.arrayColori[f].codeColori);
                    String(langue) === '1' ? tabL.push(this.produit.arrayColori[f].libcolori) : tabL.push(this.produit.arrayColori[f].libcoloriANG);
                  }
                }
                  this.arrayColorisImage = tab;
                  this.arrayColorisLib = tabC;
                  this.arrayColorisLibelle = tabL;
              }
            }
          });
        });

        this.langueService.emitLangueSelect();

        const nbTaille = (data[2].tailleDebFin).length; //permet de connaître le nombre de taille différentes sont présentes pour le produit
        for (let i = 0; i < nbTaille; i++) {
          this.tailleDisponible = this.tailleDisponible + data[2].tailleDebFin[i] + " "; //permet d'afficher dans les informations du produit les tailles disponibles
        }
        const nbColori = data[1].codeColoris.length; //retourne le nombre de coloris que possède le produit
        this.nbColori = nbColori; //affiche le nombre de coloris dans les informations du produit
      }
    ).catch((error) => {
      console.log(error);
    });

    //Activer/Désactiver les informations à afficher
    this.moduleService.VisualiseInformations().then(data => {
      this.visInformationTab = data['visInformationAff'];
      var valRefProduit = "1";
      var valTaille = "2";
      var valColoris = "3";
      var valMarque = "4";
      var valTheme = "5";
      var valFamille = "6";
      var valSousFamille = "7";
      var valModele = "8";

      //Si la ref produit est autorisé à être affiché
      this.visInformationTab.search(valRefProduit)  == -1 ? this.visRefProduit  = false  : this.visRefProduit   = true
      this.visInformationTab.search(valTaille)      == -1 ? this.visTaille      = false  : this.visTaille       = true
      this.visInformationTab.search(valColoris)     == -1 ? this.visColoris     = false  : this.visColoris      = true
      this.visInformationTab.search(valMarque)      == -1 ? this.visMarque      = false  : this.visMarque       = true
      this.visInformationTab.search(valTheme)       == -1 ? this.visTheme       = false  : this.visTheme        = true
      this.visInformationTab.search(valFamille)     == -1 ? this.visFamille     = false  : this.visFamille      = true
      this.visInformationTab.search(valSousFamille) == -1 ? this.visSousFamille = false  : this.visSousFamille  = true
      this.visInformationTab.search(valModele)      == -1 ? this.visModele      = false  : this.visModele       = true

    })

    //Activer/Désactiver les informations à afficher
    this.moduleService.libelleConstruct().then(data => {
      this.LibelleConst = data['libelleConstruct'];
      var valLib1 = "1";
      var valLib2 = "2";
      var valTheme = "3";

      this.LibelleConst.search(valLib1) == -1  ? this.lcLibelle1 = false  : this.lcLibelle1 = true
      this.LibelleConst.search(valLib2) == -1  ? this.lcLibelle2 = false  : this.lcLibelle2 = true
      this.LibelleConst.search(valTheme) == -1 ? this.lcTheme = false     : this.lcTheme = true


    })
    if (!this.leaving) {
      this.filtreService.sendCurrentLigne(this.ligne);
      this.filtreService.sendCurrentTheme(this.produit.theme);
      this.filtreService.sendCurrentColori(this.libColoriParam);
      this.filtreService.sendCurrentFamille(this.produit.famille);
      this.filtreService.sendCurrentMatiere(this.produit.texteLibre);
    }
  }

  onFocus(prix: number, quantite: number) { //lorsque l'utilisateur focus sur un input affiche le prix et la quantité pour la taille sélectionnée
    this.prix = prix;
    this.quantite = quantite;
  }

  onBlur() { //Lorsque l'utilisateur clique ailleurs dans le template remet les infos prix et quantité à 0
    this.prix = 0;
    this.quantite = 0;
  }


  onSubmit(form: NgForm) { //soumission su formulaire + ajout au panier
    for (let i = 0; i < Object.keys(form.form.value).length; i++) {
      const arrayForm = Object.keys(form.form.value);
      const indice = arrayForm[i];
    }

    this.commandeService.submitCommande(form.form.value);
    this.detailService.getDetail(this.refproduit).then(
      (data: any[]) => {
        /* Met à jour les produits de la commande dans le bloc total */
        this.commandeService.getCommande(); //récupère les produits
        this.commandeService.emitCommande(); //émet les produits dans l'ensemble de l'appli
      }
    );
  }

  //retourne aux produits
  returnBack() {
    if (this.isAuth) {
      this.router.navigate(['/contenu/produits']);
    } else {
      this.router.navigate(['/texasnet/produits']);
    }
  }

  changePhoto(lienImage: string, lienZoom: string) {
    this.image = this.imageService.PhotosArt + lienImage;
    this.imageZ = this.imageService.PhotosArt + lienZoom;
    document.getElementsByClassName("ngxImageZoomFull")[0].setAttribute("src", this.imageZ);
    document.getElementsByClassName("ngxImageZoomThumbnail")[0].setAttribute("src", this.image);
  }

  changeColoris(codecolori:string) { // $event = codeColoris
    this.detailService.getMiniatureSelonColoris(this.refproduit, codecolori, this.saison).then(
      (data: any[]) => {
        if (data['fichierMin'].length > 0) {
          this.image = this.imageService.PhotosArt + data['fichierMin'][0]
          this.imageZ = this.imageService.PhotosArt + data['fichierZoom'][0];
          this.arrayTailleImage = data['fichierMin'];
          this.arrayTailleImageZ = data['fichierZoom'];

        } else {
          console.warn(`La photo du coloris ${codecolori} pour le produit ${this.refproduit} n'est pas présente sur le serveur.`);
        }
        for (let i = 0; i < this.arrayColori.length; i++) {
          if (this.arrayColori[i].codeColori.search(codecolori) != -1) {
            this.colorPourCommandeTaille = this.arrayColori[i];
            this.filtreService.sendCurrentColori(this.arrayColori[i].libcolori);
            this.t_pvc = this.arrayColori[i].tarif_pvc;
          }
        }
        this.changePhoto(String(this.arrayTailleImage[0]), String(this.arrayTailleImageZ[0]));
      });
  }

  returnProducts() {
    //this.router.navigate(['/contenu/produits/'+this.marque]);
    this._Location.back()
  }

  ngOnDestroy(){
    this.leaving = true;
    this.filtreService.resetFiltres();
  }
}
