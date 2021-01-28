import { Component, OnInit, OnDestroy, EventEmitter, Output, Input } from '@angular/core';
import { CommandeService } from '../services/commandes.service';
import { ActivatedRoute } from '@angular/router';
import { Produits } from '../models/produits.model'
import { ProduitService } from '../services/produits.service';
import { DetailService } from '../services/detail-produit.service';
import { Detail } from '../models/detailP.models';
import { Router } from '@angular/router';
import { ModuleService} from '../services/modules.service';
import { HtmlAstPath } from '@angular/compiler';
import { BreakpointObserver } from '@angular/cdk/layout';
import { MatSnackBar } from '@angular/material/snack-bar';
import { isUndefined } from 'util';
import { Subscription } from 'rxjs';
import { NgForm } from '@angular/forms';
import { ImageService } from '../services/images.service';
import { isDefined } from '@angular/compiler/src/util';
import { ContenuProduitsDesktopComponent } from '../contenu/contenu-produits/contenu-produits-desktop/contenu-produits-desktop.component';
import { LangueService } from '../services/langue.service';
import { TranslateService,LangChangeEvent } from '@ngx-translate/core';
@Component({
  providers:[ContenuProduitsDesktopComponent],
  selector: 'app-commande-taille',
  templateUrl: './commande-taille.component.html',
  styleUrls: ['./commande-taille.component.css']
})
export class CommandeTailleComponent implements OnInit, OnDestroy {
  @Input() promo:number;
  @Input() colorFromDetailProduit: any;
  articlePromoSubscription:Subscription;
  @Output() changeColor = new EventEmitter();
  produit:Produits;
  refproduit:string;
  image:string;
  libelle:string;
  saison:string;
  codeColorisPasser:string;
  marque:string;
  codeColoris:string;
  theme:string;
  famille:string;
  sousFamille:string;
  modele:string;
  description:HtmlAstPath;
  quantite:number=0;
  prix:number=0.00;
  testQuantite:number;
  detailP:any[];
  nbColori:number; //permet de connaitre le nombre de colori pour chaque produit
  arrayColori:any[]; //permet de faie une boucle dans le template en fonction du nombre de colori présent
  tailleT:any[]; //tableau de taille des produits;
  tailleDisponible:string=" ";
  testValue:number=0;
  arrayTaille:any[]=[];
  quantiteTaille:number=0; //permet de connaître la quantité sélectionnée par le client
  plusQuantite:boolean=true //affiche le bouton plus pour ajouter des produits
  coRefProduit:string; //pour commande expresse permet de connaitre la référence du produit
  coRefProduitSubscription:Subscription;
  prixSelect:number=0; //affiche sur le template le prix de la taille sélectionnée
  quantiteSelect:number=0; //affiche sur le template la quantité de produits sélectionnés restants
  stockCouleur:boolean;
  stockCouleurSubscription:Subscription;
  stockDisponible:number;
  stockIndisponible:number;
  maxStockLimite:number;
  minStocklimite:number;
  controlStock:boolean;
  modeSaisie:number;
  arrayTailleLigne:any[];
  selectColori:number=0;
  libColorisArray:any[]=[]; //permet d'avoir le nom des coloris dans le template pour le cas d'un affichage tableau
  libColorisArrayTrad:any[]=[];
  libCodeColorisArray:any[]=[];
  libPictoArray:any[]=[];
  prixSelectPromo:number=0; //permet d'afficher le prix selectionné pour l'article en promo
  backgroundColori:string="../../Photos/Coloris/";
  deniedColor:string[]=[];
  langueSelect:any;
  langueSelected:number=1;
  langueSelectSubscription:Subscription;
  afficheSeulementSelection:boolean = false;
  commandeExpress:boolean = false;

  pasDeStock:string;
  pasDeDispo:string;
  depassStock:string;
  valeurPosit:string;
  pasDeSelect:string;
  quantiteInsuf:string;

  constructor(private router:Router,private contenuProduit:ContenuProduitsDesktopComponent, private snackBar:MatSnackBar, private BreakpointObserver:BreakpointObserver, private commandeService:CommandeService, private route:ActivatedRoute,private produitService:ProduitService, private detailService:DetailService, private moduleService:ModuleService,private imageService:ImageService, private langueService:LangueService,translate: TranslateService) {
    translate.onLangChange.subscribe((event: LangChangeEvent) => {

      translate.get('panier.noStock').subscribe((res: string) => {
        this.pasDeStock = res;
      });
      translate.get('commandetaille.pasDeDispo').subscribe((res: string) => {
        this.pasDeDispo = res;
      });
      translate.get('commandetaille.depassStock').subscribe((res: string) => {
        this.depassStock = res;
      });
      translate.get('commandetaille.valeurPosit').subscribe((res: string) => {
        this.valeurPosit = res;
      });
      translate.get('commandetaille.pasDeSelect').subscribe((res: string) => {
        this.pasDeSelect = res;
      });

    });

    translate.get('panier.noStock').subscribe((res: string) => {
      this.pasDeStock = res;
    });
    translate.get('commandetaille.pasDeDispo').subscribe((res: string) => {
      this.pasDeDispo = res;
    });
    translate.get('commandetaille.depassStock').subscribe((res: string) => {
      this.depassStock = res;
    });
    translate.get('commandetaille.valeurPosit').subscribe((res: string) => {
      this.valeurPosit = res;
    });
    translate.get('commandetaille.pasDeSelect').subscribe((res: string) => {
      this.pasDeSelect = res;
    });

    translate.onLangChange.subscribe((event: LangChangeEvent) => {
      translate.get('panier.noStock').subscribe((res: string) => {
        this.pasDeStock = res;
      });
    });
    BreakpointObserver.observe([
          '(max-width: 1050px)'
        ]).subscribe(result => {
          if (result.matches) {
            this.modeSaisie = 1;
          }else{
            this.modeSaisie = 2;
          }
        });
        this.commandeService.getCommande();
      }

  ngOnInit() {
    this.prixSelectPromo = this.route.snapshot.params['prix'];

    this.langueSelected=this.langueService.langueSelect;
    this.langueSelectSubscription=this.langueService.langueSelectSubject.subscribe(langue=>{
      this.langueSelected = langue;
    });

    if (this.router.url === "/contenu/accueil") {
      this.afficheSeulementSelection = true
    }

    this.route.queryParams.subscribe(params => {
      if(typeof(params.selec)!=='undefined') {
        this.afficheSeulementSelection = true;
      }
    });

    this.articlePromoSubscription=this.commandeService.dernierArticleEnPromoSubject.subscribe(
      (artEnPromo:number) => {
        if (typeof(artEnPromo) !== 'undefined') {
          this.promo = artEnPromo;
        }
      }
    );
    this.arrayTailleLigne=[];
    this.moduleService.getStockCouleur();
    this.stockCouleurSubscription=this.moduleService.stockCouleurSubject.subscribe(
      (stockCouleur:boolean)=>{
        this.stockCouleur=stockCouleur
      }
    );
    this.moduleService.emitStockCouleur();
    this.moduleService.stockProduit().then(
      (data)=>{
        this.stockDisponible=data["stockDisponible"];
        this.stockIndisponible=data["stockIndisponible"];
        this.maxStockLimite=data["maxStockLimite"];
        this.minStocklimite=data["minStockLimite"];
        if(data["controleStock"]==0){
          this.controlStock=false;
        }else{
          this.controlStock=true;
        }
      }
    )

    /* Dans le cas de la commande expresse  */
    if(isUndefined(this.route.snapshot.params['refproduit'])){  //il n y a pas la référence du produit en paramètre dans l'URL
      this.coRefProduitSubscription=this.commandeService.coRefProduitSubject.subscribe(
        (ref:string)=>{ //on récupère la référence depuis commandeservice en subscription

          this.arrayTaille=[];
          this.prixSelect=0;
          //remet la sélection à 0 dans toute l'array
          this.quantiteTaille=0;
          for(let i=0;i<this.arrayTaille.length;i++){
            for(let j=0;j<this.arrayTaille[i].length;j++){
              this.arrayTaille[i][j].select=0;
            }
          }
          /* Récupère en fonction de la référence du produit les détails depuis la méthode getProduit depuis produitService */
          this.produit=this.produitService.getProduit(ref);
          this.commandeExpress = true;
          this.getRefusedColor();
          for(let oneColor of this.produit.arrayColori) {
            if(!this.deniedColor.includes(oneColor.libcolori)) {
              this.colorFromDetailProduit = oneColor;
              break;
            }
          }

          this.detailService.getDetail(this.produit.refproduit).then(
            (data:any[])=>{ //récupère les détails du produit
              this.prixSelect = 0;
              //construction de arrayTaille
                  this.libColorisArray=data[1].libcoloris;
                  this.libColorisArrayTrad['FRA'] = [...data[1].libcoloris];
                  this.libColorisArrayTrad['ANG'] = [...data[1].libcolorisANG];

              //this.libColorisArray=data[1].libcoloris;
              this.libCodeColorisArray=data[1].codeColoris;
              this.libPictoArray=data[1].pictogramme;

              for (let oneRefusedColor of this.deniedColor) {
                let idx = data[1].libcoloris.indexOf(oneRefusedColor);
                data[4].splice(idx,1);
                this.libColorisArray.splice(idx,1);
                this.libPictoArray.splice(idx,1);
                this.libCodeColorisArray.splice(idx,1);
              }

              const nbColori = this.libColorisArray.length; //retourne le nombre de coloris que possède le produit

              this.arrayTaille=[];
              for(let i=0; i<nbColori;i++){
                this.arrayTaille.push(new Array);
                /*
                arrayTaille=[
                  [colori1],
                  [colori2],
                  [colori3],
                  [...]
                ];
                A chaque coloris un tableau est intégré dans arrayTaille
                */
              }
              for(let i=0;i<nbColori;i++){
                 const tailleC = Object.keys(data[4][i]).length - 1;
                if (!this.afficheSeulementSelection || (this.afficheSeulementSelection && data[4][i][0].selection === "1")) {
                  for(let j=0;j<tailleC;j++){
                    this.arrayTaille[i][j]=[];
                    this.moduleService.showMaxQty().then(data2=>{
                      const taille = data[4][i][j].taille;
                      const idproduit = data[4][i][j].idproduit;
                      const prix = data[4][i][j].prix;
                      const prixPromo = data[4][i][j].tarif_promo;
                      const coloris = data[4][i][j].libelleColoris;
                      const codeColoris = data[4][i][j].codeColoris;
                      this.codeColorisPasser = data[4][i][j].codeColoris;

                      /* Pour la quantité si le client ne souhaite pas afficher tous son stocke */
                      var quantite:number;
                      if(data2['quantiteMax']>0){ //si le paramètre est à 1 il faut masquer le stocke à partir d'une certaine quantite
                        if((data[4][i][j]).stockdisponible>data2['valQteMax']){ //pour les tailles où le stocke est supérieur à la valeur renseignée
                          quantite = data2['valQteMax']; //le stocke visible sera la valeur spécifiée par le client et non le stocke réel
                        }else{
                          /* Dans les autres cas on affiche le stocke disponible */
                          quantite = data[4][i][j].stockdisponible - data[4][i][j].stockencmd
                        }
                      }else{
                        quantite = data[4][i][j].stockdisponible - data[4][i][j].stockencmd;
                      }
                      var value=''; //initialisation du value de l'input type number
                      /* Dnas le cas où une commande est en cours indique en value dans les input la quantité de produit sélectionné */
                      if(isDefined(data[4][i][j].quantite)){
                        if(Number(data[4][i][j].quantite)!==0){ //si la quantité est supérieur à 0 affiche la value sur l'input number
                          value=data[4][i][j].quantite;
                        }
                      }else{
                        value=''; //sinon n'affiche aucune value
                      }

                      if(Number(value)>0){ //si une taille est déjà commandée par le client
                        this.arrayTaille[i][j]=new Detail(taille,quantite,idproduit,prix,value,0,Number(value),coloris,Number(prixPromo),codeColoris);
                      }else{ //sinon
                        this.arrayTaille[i][j]=new Detail(taille,quantite,idproduit,prix,value,0,0,coloris,Number(prixPromo),codeColoris);
                      }
                    });
                    /*
                    arrayTaille=[
                      colori1=[
                        {taille1,quantite1,idproduit1,prix1,value1,select1},
                        {taille2,quantite2,idproduit2,prix2,value2,select2},
                        {taille3,quantite3,idproduit3,prix3,value3,select3},
                        {...}
                      ],
                      ...
                    */
                  }
                }
              }
              this.arrayTailleLigne=this.arrayTaille[0];
            }
          );
        }
      );

      this.commandeService.emitCoRef();
    }else{ //si on appelle commande-taille dans detail-produit
      this.arrayTaille=[];
      const refproduit = this.route.snapshot.params['refproduit']; //prend la valeur du libellé en paramètre
      /* Récupère en fonction de la référence du produit les détails depuis la méthode getProduit depuis produitService */
      this.produit=this.produitService.getProduit(refproduit);
      this.getRefusedColor();
      this.detailService.getDetail(this.produit.refproduit).then(
        (data:any[])=>{ //récupère les détails du produit
          this.prixSelect = data[6]['prix'];
          //this.libColorisArray=data[1].libcoloris;
          this.libColorisArray=data[1].libcoloris;
          this.libColorisArrayTrad['FRA'] = data[1].libcoloris;
          this.libColorisArrayTrad['ANG'] = data[1].libcolorisANG;

          this.libCodeColorisArray=data[1].codeColoris;
          this.libPictoArray=data[1].pictogramme;
          const nbColori = data[1].nbColori; //retourne le nombre de coloris que possède le produit

          for (let oneRefusedColor of this.deniedColor) {
            let idx = data[1].libcoloris.indexOf(oneRefusedColor);
            data[4].splice(idx,1);
            this.libColorisArray.splice(idx,1);
            this.libPictoArray.splice(idx,1);
            this.libCodeColorisArray.splice(idx,1);
          }
          this.arrayTaille=[];
          for(let i=0; i<data[1].libcoloris.length;i++){
            this.arrayTaille.push(new Array);
          }

          /* Test promotion article */
          var testTarifPromo=0;
          for(let i=0;i<data[1].libcoloris.length;i++){
           const tailleC = Object.keys(data[4][i]).length - 1;
            for(let j=0;j<tailleC;j++){
              if(data[4][i][j].tarif_promoL!=="0.00"){
                testTarifPromo++;
              }
            }
          }

          const produit = JSON.parse(sessionStorage.getItem("produits"));
          var returnProduit = produit.find(
            (s)=>{
              return s.refproduit===refproduit;
            }
          );
          /* *********************** */
          for(let i=0;i<this.libColorisArray.length;i++){
            if (!this.afficheSeulementSelection || (this.afficheSeulementSelection && data[4][i][0].selection === "1")) {
               const tailleC = Object.keys(data[4][i]).length - 1;
              for(let j=0;j<tailleC;j++){
                this.marque = data[4][i][j].codeMarque;
                this.saison = data[4][i][j].codeSaison;

                this.arrayTaille[i][j]=[];
                this.moduleService.showMaxQty().then(data2=>{
                  const taille = data[4][i][j].taille;
                  const idproduit = data[4][i][j].idproduit;
                  const coloris = data[4][i][j].libelleColoris;
                  const codeColoris = data[4][i][j].codeColoris;

                  const prix=data[4][i][j].prix;
                  var prixPromo:number;

                  prixPromo = data[4][i][j].tarif_promo;
                  /* Pour la quantité si le client ne souhaite pas afficher tous son stocke */
                  var quantite:number;
                  if(data2['quantiteMax']>0){ //si le paramètre est à 1 il faut masquer le stocke à partir d'une certaine quantite
                    if((data[4][i][j]).stockdisponible>data2['valQteMax']){ //pour les tailles où le stocke est supérieur à la valeur renseignée
                      quantite = data2['valQteMax']; //le stocke visible sera la valeur spécifiée par le client et non le stocke réel
                    }else{
                      /* Dans les autres cas on affiche le stocke disponible */
                      quantite = data[4][i][j].stockdisponible - data[4][i][j].stockencmd
                    }
                  }else{
                    quantite = data[4][i][j].stockdisponible - data[4][i][j].stockencmd;
                  }
                  var value=''; //initialisation du value de l'input type number
                  /* Dnas le cas où une commande est en cours indique en value dans les input la quantité de produit sélectionné */
                  if(isDefined(data[4][i][j].quantite)){
                    if(Number(data[4][i][j].quantite)!==0){ //si la quantité est supérieur à 0 affiche la value sur l'input number
                      value=data[4][i][j].quantite;
                    }
                  }else{
                    value=''; //sinon n'affiche aucune value
                  }

                  if(Number(value)>0){ //si une taille est déjà commandée par le client
                    this.arrayTaille[i][j]=new Detail(taille,quantite,idproduit,prix,value,0,Number(value),coloris,Number(prixPromo),codeColoris);
                  }else{ //sinon
                    this.arrayTaille[i][j]=new Detail(taille,quantite,idproduit,prix,value,0,0,coloris,Number(prixPromo),codeColoris);
                  }
                });
              }
            }
          }
          this.arrayTailleLigne=this.arrayTaille[0];
        }
      );
    }
  }

  //méthode permettant de sélectionner une taille à commander
  selectTaille(idTaille:number,quantite:number){
    let position = 0;
    for(let i=0; i< this.arrayTaille.length; i++){
      if(this.colorFromDetailProduit.codeColori === this.arrayTaille[i][0].codeColoris){
        position = i;
      }
    }
    if(Number(quantite)>0){  //selectionnable que si la taille est en stocke
      this.plusQuantite=true;
      for(let i=0;i<this.arrayTaille.length;i++){ //parcours toutes les tailles du produit et les mets à selection 0
        for(let j=0;j<this.arrayTaille[position].length;j++){

          this.arrayTaille[i][j].select=0;
          this.prixSelect=0;
          this.quantiteSelect=0;
        }
      }
      this.arrayTaille[position][idTaille].select=1; //la taille choisie est sélectionnée
      if(this.arrayTaille[position][idTaille].quantiteTaille<1){ //si la quantité du produit sélectionnée est à 0 met la quantité à afficher sur le template à 1
        this.arrayTaille[position][idTaille].quantiteTaille=1;

      }
      this.quantiteTaille=this.arrayTaille[position][idTaille].quantiteTaille;
      this.prixSelect=this.arrayTaille[position][idTaille].prix;
      this.prixSelectPromo=this.arrayTaille[position][idTaille].prixPromo;
      this.quantiteSelect=this.arrayTaille[position][idTaille].quantite;

    }else{
      if(this.controlStock){
        this.snackBar.open(this.pasDeDispo+ " " +this.arrayTaille[position][idTaille].taille,"",{
          duration: 2000
        });
      }else{
        this.plusQuantite=true;
        for(let i=0;i<this.arrayTaille.length;i++){ //parcours toutes les tailles du produit et les mets à selection 0
          for(let j=0;j<this.arrayTaille[position].length;j++){
            this.arrayTaille[i][j].select=0;
            this.prixSelect=0;
            this.quantiteSelect=0;
          }
        }
        this.arrayTaille[position][idTaille].select=1; //la taille choisie est sélectionnée
        if(this.arrayTaille[position][idTaille].quantiteTaille<1){ //si la quantité du produit sélectionnée est à 0 met la quantité à afficher sur le template à 1
          this.arrayTaille[position][idTaille].quantiteTaille=1;

        }
        this.quantiteTaille=this.arrayTaille[position][idTaille].quantiteTaille;
        this.prixSelect=this.arrayTaille[position][idTaille].prix;
        this.prixSelectPromo=this.arrayTaille[position][idTaille].prixPromo;
        this.quantiteSelect=this.arrayTaille[position][idTaille].quantite;
      }
    }
  }



  quantiteMoins(){ //lorsqu'une taille est sélectionnée enlève une quantité
    for(let i=0;i<this.arrayTaille.length;i++){ //récupère le produit sélectionné
      for(let j=0;j<this.arrayTaille[i].length;j++){
        if(this.arrayTaille[i][j].select===1){ //le produit sélectionné esst trouvé
          this.arrayTaille[i][j].quantiteTaille-=1; //on enlève une quantité
          this.quantiteTaille=this.arrayTaille[i][j].quantiteTaille; //on affiche la quantité sélectionné pour le produit sur le template
          this.plusQuantite=true;
        }
      }
    }
    if(this.controlStock){
      if(this.quantiteTaille===0){
        for(let i=0;i<this.arrayTaille.length;i++){
          for(let j=0;j<this.arrayTaille[i].length;j++){
            this.arrayTaille[i][j].select=0;
            this.prixSelect=0;
          }
        }
      }
    }
  }

  quantitePlus(){ //lorsqu'une taille est sélectionnée ajoute une quantité
    for(let i=0;i<this.arrayTaille.length;i++){ //récupère le produit sélectionné
      for(let j=0;j<this.arrayTaille[i].length;j++){
        if(this.arrayTaille[i][j].select===1){ //le produit sélectionné est trouvé
          if(this.arrayTaille[i][j].quantiteTaille<this.arrayTaille[i][j].quantite){
            this.arrayTaille[i][j].quantiteTaille+=1 //on ajoute une quantité
            this.quantiteTaille=this.arrayTaille[i][j].quantiteTaille;
          }else if(!this.controlStock){
            this.arrayTaille[i][j].quantiteTaille+=1 //on ajoute une quantité
            this.quantiteTaille=this.arrayTaille[i][j].quantiteTaille;
          }
          if(this.controlStock){
            if(this.arrayTaille[i][j].quantiteTaille===Number(this.arrayTaille[i][j].quantite)){ //quand la quantité est atteinte cache le bouton +
              this.plusQuantite=false;
            }
          }
        }else{
          this.plusQuantite=true
        }
      }
    }
  }

  updateQteLigne(form:NgForm){
    let currentQte = 0;
    let currentLine = 0;
    let currentPrice = 0;
    let line = 0;
    let col = 0;
    let stkDepasse = false;

    let o = 0;
    for(let prop in form.form.value){
      if(`${form.form.value[prop]}`!==""){
        o++
        let tmp = prop.split('-');
        let x = tmp[0];
        let y = tmp[1];
        if(form.form.value[prop] > this.arrayTaille[x][y].quantite){
          stkDepasse = true;
          alert(this.depassStock);
          break;
        }
      }
    }

    if (!stkDepasse) {
        // On transforme le formulaire en array parce que for ... in ne garde pas forcement l'ordre des props
      let arrayForm = Object.keys(form.value);
      arrayForm.sort((a, b) => {
        let ia1 = a.indexOf('-');
        let ia2 = a.indexOf('-', ia1+1);
        let ib1 = b.indexOf('-');
        let ib2 = b.indexOf('-', ib1+1);

        if (a.substring(0,ia1) !== b.substring(0,ib1)) {
          return Number(a.substring(0,ia1))-Number(b.substring(0,ib1));
        } else {
          return Number(a.substring(ia1+1,ia2))-Number(b.substring(ib1+1,ib2));
        }
      });
        // On parcourt chaque valeur du formulaire, les données doivent être ordonnées par ligne
      for(let prop of arrayForm) {
          // Récupère la ligne (qts) et la colonne (prix)
        let words = prop.split('-');
        line = parseInt(words[0]);
        col = parseInt(words[1]);
          // Assignation des valeurs en fin de traitement : on ajoute les données + réinitialise quand la ligne de l'élément suivant est différente de celle en cours
        if (line !== currentLine) {
            // On est obligé de rajouter les valeurs dans arrayTaille car c'est celui qui est utilisé dans le *ngFor
          this.arrayTaille[currentLine].totLigneQte = currentQte;
          this.arrayTaille[currentLine].totLignePrix = currentPrice;
          currentLine = line;
          currentQte = 0;
          currentPrice = 0;
        }
          // Si la qte est valide, on l'ajoute a notre total par ligne et on ajoute le prix
        if ( form.form.value[prop] !== "" && form.form.value[prop] !== null) {
          currentQte += parseInt(form.form.value[prop]);
          if(typeof(this.promo) === 'undefined' || String(this.promo) === '0.00') {
            //currentPrice += (parseInt(form.form.value[prop]) * parseInt(this.arrayTaille[currentLine][col].prix));
            currentPrice += (parseInt(form.form.value[prop]) * this.arrayTaille[currentLine][col].prix);
          } else {
            currentPrice += (parseInt(form.form.value[prop]) * this.arrayTaille[currentLine][col].prixPromo);
          }
        }
      }
        // Vu qu'on traite par rapport à l'élément suivant, pour la derniere ligne on retraite automatiquement
      this.arrayTaille[currentLine]["totLigneQte"] = currentQte;
      this.arrayTaille[currentLine]["totLignePrix"] = currentPrice;
    }
  }

  //Méthode pour ajouter au panier le produit
  onAjoutPanier(checkQuantite:boolean = false){
    if (checkQuantite && this.quantiteTaille <= 0) return;
    //this.commandeService.ajoutPanier(); // appel de la méthode ajout panier depuis le service de commande
    for(let i=0;i<this.arrayTaille.length;i++){
      for(let j=0;j<this.arrayTaille[i].length;j++){
        if(this.arrayTaille[i][j].select===1){
          const idproduit=this.arrayTaille[i][j].idproduit;
          const quantiteSelect=this.arrayTaille[i][j].quantiteTaille;
          const codeColoris=this.arrayTaille[i][j].codeColoris;
          this.commandeService.ajoutPanier(idproduit,quantiteSelect,this.marque,this.saison,codeColoris);
          this.commandeService.getCommande(); //récupère les produits
          this.commandeService.emitCommande(); //émet les produits dans l'ensemble de l'appli
        }
      }
    }
    //remet la sélection à 0 dans toute l'array
    this.ngOnInit();
    this.quantiteSelect=0;
    this.quantiteTaille=0;
    for(let i=0;i<this.arrayTaille.length;i++){
      for(let j=0;j<this.arrayTaille[i].length;j++){
        this.arrayTaille[i][j].select=0;
      }
    }
  }

  detectColori(idColori:number, unArrayColori){
    //this.arrayTailleLigne=this.arrayTaille[idColori]
    this.arrayTailleLigne=this.arrayTaille[idColori];
    this.selectColori=idColori;
    this.colorFromDetailProduit = unArrayColori;
  }

  /* Affiche une snackbar indiquant qu'il n'y a pas de stock */
  noStock(taille){
    if(this.controlStock){
      this.snackBar.open(this.pasDeStock+" "+taille,"",{
        duration:3000
      });
    }
  }

  prixSelectTableau(i:number,j:number){
    this.prixSelect=this.arrayTaille[i][j].prix;
    this.prixSelectPromo=this.arrayTaille[i][j].prixPromo;
  }

  onSubmitTableauCommande(form:NgForm){
    var i=0;
    for(let prop in form.form.value){
      if(`${form.form.value[prop]}`!==""){
        i++
        var words = prop.split('-');
        var val_i = words[0];
        var val_j = words[1];
        if(form.form.value[prop] > this.arrayTaille[val_i][val_j].quantite ){
          /*this.snackBar.open(this.depassStock,"",{
            duration:3000
          });*/
          alert(this.depassStock)
        } else if (form.form.value[prop] < 0){
          this.snackBar.open(this.valeurPosit,"",{
            duration:3000
          });
        } else {
          this.commandeService.ajoutPanier(this.arrayTaille[val_i][val_j].idproduit,form.form.value[prop],this.marque,this.saison,this.arrayTaille[val_i][val_j].codeColoris);
        }
      }
    }
    if(i===0){
      this.snackBar.open(this.pasDeSelect,"",{
        duration:3000
      });
    }
  }

  getRefusedColor() {
    this.deniedColor = [];
    if (typeof(this.produit) !== 'undefined'){
      for (let arrColor of this.produit.arrayColori) {
        if (this.promo > 0 && arrColor.tarif_promo !== this.promo) {
          this.deniedColor.push(arrColor.libcolori);
        } else if ((typeof(this.promo) === 'undefined' || String(this.promo) === '0.00')  && arrColor.tarif_promo > 0) {
          this.deniedColor.push(arrColor.libcolori);
        } else if (this.afficheSeulementSelection && arrColor.selection === "0"){
          this.deniedColor.push(arrColor.libcolori);
        }
      }
    }
  }

  coloriValide(colori:string) {
    return !this.deniedColor.includes(colori);
  }

  ngOnDestroy(){
    if(isUndefined(this.route.snapshot.params['refproduit'])){ //utilisation de la subscription seulement si commande expresse
      this.coRefProduitSubscription.unsubscribe();
    }
    this.stockCouleurSubscription.unsubscribe();
  }

  closeModal(){
    var event = document.createEvent("HTMLEvents");
    event.initEvent("click", true, true);
    var button = document.getElementsByClassName('close')[0];
    button.dispatchEvent(event);
  }
}
