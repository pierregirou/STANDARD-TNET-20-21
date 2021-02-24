import { Produits } from '../models/produits.model';
import { Subject } from 'rxjs';
import { HttpClient } from '@angular/common/http';
import { Injectable, OnInit } from '@angular/core';
import { ModuleService } from '../services/modules.service';
import { DeviceDetectorService } from 'ngx-device-detector';
import { HttpRequest } from '../services/http-request.service';
import { ImageService } from "../services/images.service";
import { Colori } from '../models/coloris.models';
import { TailleFiltre } from '../models/tailleFiltre.models';

@Injectable()
export class ProduitService implements OnInit{
    isDesktop:boolean; //booléenne true si le périphérique est un pc
    isMobile:boolean; //booléenne true si le périphérique est un mobile
    isTablet:boolean; //booléenne true si le périphérique est une tablette tactile
    visGalerie:boolean;
    public produits:Produits[]=[];
    private produitsSearch:Produits[]=[];
    private produitsGestion:Produits[]=[];
    private produitSelection:Produits[]=[];
    private produitPromo:Produits[]=[];
    produitSubject=new Subject<Produits[]>();
    produitSubjectGestion=new Subject<Produits[]>();
    ProduitSelectionSubject=new Subject<Produits[]>();
    produitPromoSubject=new Subject<Produits[]>();
    /* Array pour le filtre colori */
    coloriFiltre:Colori[]=[];
    coloriFiltreBox:any[];
    coloriFiltreSubject=new Subject<Colori[]>();
    /* Array pour le filtre taille */
    tailleFiltre:TailleFiltre[];
    tailleFiltreSubject=new Subject<TailleFiltre[]>();
    colorisFiltreBoxSubject=new Subject<any[]>();
    matiereFiltreBox:any[];
    matiereFiltreBoxSubject=new Subject<any[]>();
    ligneFiltreBox:any[];
    ligneFiltreBoxSubject=new Subject<any[]>();
    familleFiltreBox:any[];
    familleFiltreBoxSubject=new Subject<any[]>();
    themeFiltreBox:any[];
    themeFiltreBoxSubject=new Subject<any[]>();
    sousFamilleFiltreBox: any[];
    sousFamilleFiltreBoxSubject=new Subject<any[]>();
    marqueFiltreBox: any[];
    marqueFiltreBoxSubject=new Subject<any[]>();
    modeleFiltreBox: any[];
    modeleFiltreBoxSubject=new Subject<any[]>();


    ligneSubject=new Subject<any>();
    currentLigne:string;

    mode:string;
    constructor(private imageService:ImageService,private httpClient : HttpClient,private moduleService:ModuleService,private detectService:DeviceDetectorService, private httpRequest:HttpRequest){
        this.detectMobile();
    }

    emitProduits(){
        this.produitSubject.next(this.produits.slice());
    }

    emitProduitsGestion(){
        this.produitSubjectGestion.next(this.produitsGestion.slice());
    }

    emitProduitSelection(){
        this.ProduitSelectionSubject.next(this.produitSelection.slice());
    }

    emitProduitPromo(){
        this.produitPromoSubject.next(this.produitPromo.slice());
    }

    getProduit(refproduit:string){ //méthode permettant de rechercher un produit dans produits
        const produitStorage = JSON.parse(sessionStorage.getItem("produits"));
        var produitFind = produitStorage.find(
            (s)=>{
                return s.refproduit===refproduit;
            }
        )
        return produitFind
    }

    returnProduits() {
      return this.produits;
    }

    ngOnInit(){}

    recupProduit(langue){ //méthode pour récupérer les produits depuis info-produit.php en fonction de l'utilisateur
        if(sessionStorage.getItem("isLoggedIn")==='true'){
            //détermine le mode d'affichage pour les produits 1-->ligne 2-->tableau
            this.moduleService.modeSaisie().then(data=>{
                if(data==="1"){
                  if(sessionStorage.getItem("produits") !== null){
                    const array = []
                    const getStringItem = sessionStorage.getItem("produits");
                    array.push(JSON.parse(getStringItem))
                    this.produits = array
                    this.emitProduits(); // produits est déclaré en private permet de l'émettre dans le reste de l'appli
                    this.recupColoriFiltre();
                  }else{
                    this.httpClient.post(this.httpRequest.InfoProduit,{
                        "login":sessionStorage.getItem("loginCompte"), //les produits retournés correspondent à ceux associés au codeTarif du client
                        "type":"login", //spécifie à l'API la réception des produits de l'utilisateur associé à son code tarif
                        "mode":"ligne"
                    }).subscribe(data=>{
                        var keys = Object.keys(data); //transforme l'objet data retourné par httpClient en un tableau
                        var len = keys.length; //récupère la longueur de ce tableau
                        this.produits=[]; //rénitialise le tableau produit à chaque appel de la fonction
                        if(len>0){
                            var j=0;
                            for(let i=1;i<len;i++){
                                this.produits[Number(data[i].positionGalerie)-1]=new Produits(Number(data[i].idproduit),
                                data[i].codeSaison,
                                data[i].libelle,
                                data[i].refproduit, //reference du produit
                                data[i].prix, //prix
                                this.imageService.PhotosArt+"/"+data[i].imageArt, //image
                                '', //taille
                                data[i].codeColori, // coloris
                                data[i].codeMarque, // marque
                                data[i].codeTheme, // Theme
                                data[i].codeFamille, // Famille
                                data[i].codeSousFamille, // sous famille
                                data[i].codeModele, // modele
                                Number(data[i].positionGalerie),
                                data[i].promo,
                                data[i]["arrayColori"],
                                data[i].nbRef,
                                data[i].selection,
                                data[i].tarif_promo,
                                data[i].libcolori,
                                data[i].codetarif,
                                data[i].tarif_pvc,
                                data[i]["codeColoris"],
                                data[i]["imageMiniature"],
                                data[i].libelle2,
                                data[i].texteLibre,
                                data[i].champsstat,
                                data[i].imageZoom,
                                data[i].arrayTarif,
                                data[i].codeLigne,
                                data[i].libelleANG
                                );
                                j++;
                            }
                        }
                        sessionStorage.setItem("produits",JSON.stringify(this.produits)); // stocke dans un sessionStorage les produits de l'utilisateur pendant sa connexion
                        // NB: On peut seulement spécifier un type string dans un session storage
                        this.emitProduits(); // produits est déclaré en private permet de l'émettre dans le reste de l'appli
                        this.recupColoriFiltre();
                    })
                  }
                }
                if(data==="2"){
                  if(sessionStorage.getItem("produits") !== null){
                    const array = []
                    const getStringItem = sessionStorage.getItem("produits");
                    array.push(JSON.parse(getStringItem))
                    this.produits = array
                    this.emitProduits(); // produits est déclaré en private permet de l'émettre dans le reste de l'appli
                    this.recupColoriFiltre();
                  }else{
                    this.httpClient.post(this.httpRequest.InfoProduit,{
                        "login":sessionStorage.getItem("loginCompte"), //les produits retournés correspondent à ceux associés au codeTarif du client
                        "type":"login", //spécifie à l'API la réception des produits de l'utilisateur associé à son code tarif
                        "mode":"tableau",
                        "langue":langue
                    }).subscribe(data=>{
                      if(data !== null && data !== undefined) {
                          var keys = Object.keys(data); //transforme l'objet data retourné par httpClient en un tableau
                          var len = keys.length; //récupère la longueur de ce tableau
                          this.produits=[]; //rénitialise le tableau produit à chaque appel de la fonction
                          if(len>0){
                              var j=0;
                              for(let i=1;i<len;i++){
                                  this.produits[Number(data[i].positionGalerie)-1]=new Produits(Number(data[i].idproduit),
                                  data[i].codeSaison,
                                  data[i].libelle,
                                  data[i].refproduit, //reference du produit
                                  data[i].prix, //prix
                                  this.imageService.PhotosArt+"/"+data[i].imageArt, //image
                                  '', //taille
                                  data[i].codeColori, //coloris
                                  data[i].codeMarque, //marque
                                  data[i].codeTheme, //Theme
                                  data[i].codeFamille, //Famille
                                  data[i].codeSousFamille, //sous famille
                                  data[i].codeModele, //modele
                                  Number(data[i].positionGalerie),
                                  data[i].promo,
                                  data[i]["arrayColori"],
                                  data[i].nbRef,
                                  data[i].selection,
                                  data[i].tarif_promo,
                                  data[i].libcolori,
                                  data[i].codetarif,
                                  data[i].tarif_pvc,
                                  data[i]["codeColoris"],
                                  data[i]["imageMiniature"],
                                  data[i].libelle2,
                                  data[i].texteLibre,
                                  data[i].champsstat,
                                  data[i].imageZoom,
                                  data[i].arrayTarif,
                                  data[i].codeLigne,
                                  data[i].libelleANG
                                  );
                                  j++;
                              }
                          }
                          sessionStorage.setItem("produits",JSON.stringify(this.produits)); //stocke dans un sessionStorage les produits de l'utilisateur pendant sa connexion
                          //NB: On peut seulement spécifier un type string dans un session storage
                          this.emitProduits(); // produits est déclaré en private permet de l'émettre dans le reste de l'appli
                          this.recupColoriFiltre();
                        }
                    })
                  }
                }
            })

        }
        this.moduleService.visGalerieStatus().then(
            (status)=>{
                if(status){
                  if(sessionStorage.getItem("produits") !== null){
                    const array = []
                    const getStringItem = sessionStorage.getItem("produits");
                    array.push(JSON.parse(getStringItem))
                    this.produits = array
                    this.emitProduits(); // produits est déclaré en private permet de l'émettre dans le reste de l'appli
                  }else{
                    if(!(sessionStorage.getItem("isLoggedIn")=='true')){
                        this.httpClient.post(this.httpRequest.InfoProduit,{
                            "visGalerie":"true" //les produits retournés correspondent à ceux associés au codeTarif du client
                        }).subscribe(data=>{
                        var keys = Object.keys(data); //transforme l'objet data retourné par httpClient en un tableau
                        var len = keys.length; //récupère la longueur de ce tableau
                        this.produits=[]; //rénitialise le tableau produit à chaque appel de la fonction
                        if(len>0){
                            var j=0;
                            for(let i=1;i<len;i++){
                                this.produits[Number(data[i].positionGalerie)-1]=new Produits(Number(data[i].idproduit),
                                data[i].codeSaison,
                                data[i].libelle,
                                data[i].refproduit, // reference du produit
                                data[i].prix, // prix
                                this.imageService.PhotosArt+"/"+data[i].imageArt, // image
                                '', // taille
                                data[i].codeColori, // coloris
                                data[i].codeMarque, // marque
                                data[i].codeTheme, // Theme
                                data[i].codeFamille, // Famille
                                data[i].codeSousFamille, // sous famille
                                data[i].codeModele, // modele
                                Number(data[i].positionGalerie),
                                data[i].promo,
                                data[i]["arrayColori"],
                                data[i].nbRef,
                                data[i].selection,
                                data[i].tarif_promo,
                                data[i].libcolori,
                                data[i].codetarif,
                                data[i].tarif_pvc,
                                data[i]["codeColoris"],
                                data[i]["imageMiniature"],
                                data[i].libelle2,
                                data[i].texteLibre,
                                data[i].champsstat,
                                data[i].imageZoom,
                                data[i].arrayTarif,
                                data[i].codeLigne,
                                data[i].libelleANG
                                );
                                j++;
                            }
                        }
                        sessionStorage.setItem("produits",JSON.stringify(this.produits)); //stocke dans un sessionStorage les produits de l'utilisateur pendant sa connexion
                        //NB: On peut seulement spécifier un type string dans un session storage
                        this.emitProduits(); // produits est déclaré en private permet de l'émettre dans le reste de l'appli
                        })
                    }
                  }
                }
            }
        )
    }

    recupProduitGestion(){ //récupère tous les produits pour pouvoir modifier leur position dans la galerie
        if(sessionStorage.getItem("admin")==='true'){
            this.httpClient.post(this.httpRequest.InfoProduit,{
                "login":sessionStorage.getItem("loginCompte"), //les produits retournés correspondent à ceux associés au codeTarif du client
                "type":"gestion" //spécifie à l'API la réception des produits de l'utilisateur associé à son code tarif
            }).subscribe(data=>{
                var keys = Object.keys(data); //transforme l'objet data retourné par httpClient en un tableau
                var len = keys.length; //récupère la longueur de ce tableau
                this.produitsGestion=[]; //rénitialise le tableau produit à chaque appel de la fonction
                if(len>0){
                    for(let i=1;i<len;i++){
                        //this.produitsGestion[Number(data[i].positionGalerie[0])-1]=new Produits(Number(data[i].idproduit),
                        this.produitsGestion[i-1]=new Produits(Number(data[i].idproduit),
                        data[i].codeSaison,
                        data[i].libelle,
                        data[i].refproduit, // reference du produit
                        data[i].prix, // prix
                        this.imageService.PhotosArt+"/"+data[i].imageArt, // image
                        '', // taille
                        data[i].codeColori, // coloris
                        data[i].codeMarque, // marque
                        data[i].codeTheme, // Theme
                        data[i].codeFamille, // Famille
                        data[i].codeSousFamille, // sous famille
                        data[i].codeModele, // modele
                        data[i].positionGalerie,
                        data[i].promo,
                        data[i]["arrayColori"],
                        data[i].nbRef,
                        data[i].selection,
                        data[i].tarif_promo,
                        data[i].libcolori,
                        data[i].codetarif,
                        data[i].tarif_pvc,
                        data[i]["codeColoris"],
                        data[i]["imageMiniature"],
                        data[i].libelle2,
                        data[i].texteLibre,
                        data[i].champsstat,
                        data[i].imageZoom,
                        data[i].arrayTarif,
                        data[i].codeLigne,
                        data[i].libelleANG
                        );
                        if (i === len-1) this.emitProduitsGestion();
                    }
                }
                this.emitProduitsGestion();// produits est déclaré en private permet de l'émettre dans le reste de l'appli
            })
        }
    }

    /* Récupe de la selection du moment */

    recupSelection(langue){
        if(sessionStorage.getItem("isLoggedIn")=="true"){
            this.httpClient.post(this.httpRequest.InfoProduit,{
                "login":sessionStorage.getItem("loginCompte"), //les produits retournés correspondent à ceux associés au codeTarif du client
                "type":"selection", //spécifie à l'API la réception des produits de l'utilisateur associé à son code tarif
                "langue":langue
            }).subscribe(data=>{
                var keys = Object.keys(data); //transforme l'objet data retourné par httpClient en un tableau
                var len = keys.length; //récupère la longueur de ce tableau
                this.produitSelection=[]; //rénitialise le tableau produit à chaque appel de la fonction
                if(len>0){
                    var j=0;
                    for(let i=1;i<len;i++){
                        this.produitSelection[j]=new Produits(Number(data[i].idproduit),
                        data[i].codeSaison,
                        data[i].libelle,
                        data[i].refproduit, //reference du produit
                        data[i].prix, //prix
                        this.imageService.PhotosArt+"/"+data[i].imageArt, //image
                        '', //taille
                        data[i].codeColori, //coloris
                        data[i].codeMarque, //marque
                        data[i].codeTheme, //Theme
                        data[i].codeFamille, //Famille
                        data[i].codeSousFamille, //sous famille
                        data[i].codeModele, //modele
                        data[i].positionGalerie,
                        data[i].promo,
                        //data[i].libcolori,
                        data[i]["arrayColori"],
                        1,
                        data[i].selection,
                        data[i].tarif_promo,
                        data[i].libcolori,
                        data[i].codetarif,
                        data[i].tarif_pvc,
                        data[i]["codeColoris"],
                        data[i]["imageMiniature"],
                        data[i].libelle2,
                        data[i].texteLibre,
                        data[i].champsstat,
                        data[i].imageZoom,
                        data[i].arrayTarif,
                        data[i].codeLigne,
                        data[i].libelleANG
                        );
                        j++;
                    }
                }
                this.emitProduitSelection(); // produits est déclaré en private permet de l'émettre dans le reste de l'appli
            })
        } else {
          this.produitSelection=[];
          this.emitProduitSelection();
        }
    }


    /* Récupe de la selection du moment */

    recupPromo(langue){
      if(sessionStorage.getItem("PROMOproduits") !== null){
        const array = []
        const getStringItem = sessionStorage.getItem("PROMOproduits");
        array.push(JSON.parse(getStringItem))
        this.produits = array
        this.emitProduitPromo(); // produits est déclaré en private permet de l'émettre dans le reste de l'appli
      }else{
        this.httpClient.post(this.httpRequest.InfoProduit,{
            "login":sessionStorage.getItem("loginCompte"),
            "type":"promo",
            "langue":langue
        }).subscribe(data=>{
            var keys = Object.keys(data); //transforme l'objet data retourné par httpClient en un tableau
            var len = keys.length; //récupère la longueur de ce tableau
            this.produits=[]; //rénitialise le tableau produit à chaque appel de la fonction
            if(len>0){
                this.produitPromo=[];
                var j=0;
                for(let i=1;i<len;i++){
                    this.produitPromo[j]=new Produits(Number(data[i].idproduit),
                    data[i].codeSaison,
                    data[i].libelle,
                    data[i].refproduit, //reference du produit
                    data[i].prix, //prix
                    this.imageService.PhotosArt+"/"+data[i].imageArt, //image
                    '', //taille
                    data[i].codeColori, //coloris
                    data[i].codeMarque, //marque
                    data[i].codeTheme, //Theme
                    data[i].codeFamille, //Famille
                    data[i].codeSousFamille, //sous famille
                    data[i].codeModele, //modele
                    Number(data[i].positionGalerie),
                    data[i].promo,
                    data[i]["arrayColori"],
                    data[i].nbRef,
                    data[i].selection,
                    data[i].tarif_promo,
                    data[i].libcolori,
                    data[i].codetarif,
                    data[i].tarif_pvc,
                    data[i]["codeColoris"],
                    data[i]["imageMiniature"],
                    data[i].libelle2,
                    data[i].texteLibre,
                    data[i].champsstat,
                    data[i].imageZoom,
                    data[i].arrayTarif,
                    data[i].codeLigne,
                    data[i].libelleANG
                    );
                    j++;
                }
            }
            this.emitProduitPromo(); // produits est déclaré en private permet de l'émettre dans le reste de l'appli
        })
      }
    }

    /****************************************** Filtre  ***********************************************/

    /* Pour les coloris */

    recupColoriFiltre(){
      let colorPresent = [];
      this.coloriFiltre=[]; // rénitialise l'array es coloris
      for(let unProduit of this.produits) {
        if(unProduit.arrayColori){
          for(let uneCouleur of unProduit.arrayColori) {
            if(colorPresent.indexOf(uneCouleur.libcolori) === -1) {
                colorPresent.push(uneCouleur.libcolori);
            }
          }
         }
      }
      colorPresent.sort();
      for(let uneCouleur2 of colorPresent) {
        this.coloriFiltre.push(new Colori(uneCouleur2,'red'));
      }
      this.emitColori();
    }

    /* Méthode apply filtre */
    filtreApply(type:string,array:any[],mode:string,url:string=''){
        this.moduleService.modeSaisie().then(data=>{
            if(type=='colori'){
                if(data==="1"){
                    this.httpClient.post(this.httpRequest.FiltreApply,{
                        "login":sessionStorage.getItem("loginCompte"), //les produits retournés correspondent à ceux associés au codeTarif du client
                        "type":"colori", //spécifie d'appliquer le filtre sur les coloris
                        "mode":"ligne",
                        "array":array
                    }).subscribe(data=>{
                        var keys = Object.keys(data); //transforme l'objet data retourné par httpClient en un tableau
                        var len = keys.length; //récupère la longueur de ce tableau
                        this.produits=[]; //rénitialise le tableau produit à chaque appel de la fonction
                        if(len>0){
                            var j=0;
                            for(let i=1;i<len;i++){
                                this.produits[Number(data[i].positionGalerie)-1]=new Produits(Number(data[i].idproduit),
                                data[i].codeSaison,
                                data[i].libelle,
                                data[i].refproduit, //reference du produit
                                data[i].prix, //prix
                                this.imageService.PhotosArt+"/"+data[i].imageArt, //image
                                '', //taille
                                data[i].codeColori, //coloris
                                data[i].codeMarque, //marque
                                data[i].codeTheme, //Theme
                                data[i].codeFamille, //Famille
                                data[i].codeSousFamille, //sous famille
                                data[i].codeModele, //modele
                                Number(data[i].positionGalerie),
                                data[i].promo,
                                data[i]["arrayColori"],
                                data[i].nbRef,
                                data[i].selection,
                                data[i].tarif_promo,
                                data[i].libcolori,
                                data[i].codetarif,
                                data[i].tarif_pvc,
                                data[i]["codeColoris"],
                                data[i]["imageMiniature"],
                                data[i].libelle2,
                                data[i].texteLibre,
                                data[i].champsstat,
                                data[i].imageZoom,
                                data[i].arrayTarif,
                                data[i].codeLigne,
                                data[i].libelleANG
                                );
                                j++;
                            }
                        }
                        this.emitProduits();
                    })
                }
                if(data==="2"){
                  if(sessionStorage.getItem("produits") !== null){
                    const array = []
                    const getStringItem = sessionStorage.getItem("produits");
                    array.push(JSON.parse(getStringItem))
                    this.produits = array
                    this.emitProduits();
                  }else{
                    this.httpClient.post(this.httpRequest.InfoProduit,{
                        "login":sessionStorage.getItem("loginCompte"), //les produits retournés correspondent à ceux associés au codeTarif du client
                        "mode":"tableau",
                        "type":"login",
                        "url":url,
                        "array":array
                    }).subscribe(data=>{
                        var keys = Object.keys(data); //transforme l'objet data retourné par httpClient en un tableau
                        var len = keys.length; //récupère la longueur de ce tableau
                        this.produits=[]; //rénitialise le tableau produit à chaque appel de la fonction
                        if(len>0){
                            var j=0;
                            for(let i=1;i<len;i++){
                                this.produits[Number(data[i].positionGalerie)-1]=new Produits(Number(data[i].idproduit),
                                data[i].codeSaison,
                                data[i].libelle,
                                data[i].refproduit, //reference du produit
                                data[i].prix, //prix
                                this.imageService.PhotosArt+"/"+data[i].imageArt, //image
                                '', //taille
                                data[i].codeColori, //coloris
                                data[i].codeMarque, //marque
                                data[i].codeTheme, //Theme
                                data[i].codeFamille, //Famille
                                data[i].codeSousFamille, //sous famille
                                data[i].codeModele, //modele
                                Number(data[i].positionGalerie),
                                data[i].promo,
                                data[i]["arrayColori"],
                                data[i].nbRef,
                                data[i].selection,
                                data[i].tarif_promo,
                                data[i].libcolori,
                                data[i].codetarif,
                                data[i].tarif_pvc,
                                data[i]["codeColoris"],
                                data[i]["imageMiniature"],
                                data[i].libelle2,
                                data[i].texteLibre,
                                data[i].champsstat,
                                data[i].imageZoom,
                                data[i].arrayTarif,
                                data[i].codeLigne,
                                data[i].libelleANG
                                );
                                j++;
                            }
                        }
                        this.emitProduits();
                    })
                  }
                }
            }
            if(type=='taille'){
                if(data==="1"){
                    this.httpClient.post(this.httpRequest.FiltreApply,{
                        "login":sessionStorage.getItem("loginCompte"), //les produits retournés correspondent à ceux associés au codeTarif du client
                        "type":"taille",
                        "mode":"tableau",
                        "array":array
                    }).subscribe(data=>{
                        var keys = Object.keys(data); //transforme l'objet data retourné par httpClient en un tableau
                        var len = keys.length; //récupère la longueur de ce tableau
                        this.produits=[]; //rénitialise le tableau produit à chaque appel de la fonction
                        if(len>0){
                            var j=0;
                            for(let i=1;i<len;i++){
                                this.produits[Number(data[i].positionGalerie)-1]=new Produits(Number(data[i].idproduit),
                                data[i].codeSaison,
                                data[i].libelle,
                                data[i].refproduit, //reference du produit
                                data[i].prix, //prix
                                this.imageService.PhotosArt+"/"+data[i].imageArt, //image
                                '', //taille
                                data[i].codeColori, //coloris
                                data[i].codeMarque, //marque
                                data[i].codeTheme, //Theme
                                data[i].codeFamille, //Famille
                                data[i].codeSousFamille, //sous famille
                                data[i].codeModele, //modele
                                Number(data[i].positionGalerie),
                                data[i].promo,
                                data[i]["arrayColori"],
                                data[i].nbRef,
                                data[i].selection,
                                data[i].tarif_promo,
                                data[i].libcolori,
                                data[i].codetarif,
                                data[i].tarif_pvc,
                                data[i]["codeColoris"],
                                data[i]["imageMiniature"],
                                data[i].libelle2,
                                data[i].texteLibre,
                                data[i].champsstat,
                                data[i].imageZoom,
                                data[i].arrayTarif,
                                data[i].codeLigne,
                                data[i].libelleANG
                                );
                                j++;
                            }
                        }
                        this.emitProduits();
                    });
                }
                if(data==="2"){
                    this.httpClient.post(this.httpRequest.FiltreApply,{
                        "login":sessionStorage.getItem("loginCompte"), //les produits retournés correspondent à ceux associés au codeTarif du client
                        "type":"taille",
                        "mode":"ligne",
                        "array":array
                    }).subscribe(data=>{
                        var keys = Object.keys(data); //transforme l'objet data retourné par httpClient en un tableau
                        var len = keys.length; //récupère la longueur de ce tableau
                        this.produits=[]; //rénitialise le tableau produit à chaque appel de la fonction
                        if(len>0){
                            var j=0;
                            for(let i=1;i<len;i++){
                                this.produits[Number(data[i].positionGalerie)-1]=new Produits(Number(data[i].idproduit),
                                data[i].codeSaison,
                                data[i].libelle,
                                data[i].refproduit, //reference du produit
                                data[i].prix, //prix
                                this.imageService.PhotosArt+"/"+data[i].imageArt, //image
                                '', //taille
                                data[i].codeColori, //coloris
                                data[i].codeMarque, //marque
                                data[i].codeTheme, //Theme
                                data[i].codeFamille, //Famille
                                data[i].codeSousFamille, //sous famille
                                data[i].codeModele, //modele
                                Number(data[i].positionGalerie),
                                data[i].promo,
                                data[i]["arrayColori"],
                                data[i].nbRef,
                                data[i].selection,
                                data[i].tarif_promo,
                                data[i].libcolori,
                                data[i].codetarif,
                                data[i].tarif_pvc,
                                data[i]["codeColoris"],
                                data[i]["imageMiniature"],
                                data[i].libelle2,
                                data[i].texteLibre,
                                data[i].champsstat,
                                data[i].imageZoom,
                                data[i].arrayTarif,
                                data[i].codeLigne,
                                data[i].libelleANG
                                );
                                j++;
                            }
                        }
                        this.emitProduits();
                    });
                }
            }
        })
    }

    filtreApply2(params:any[]){
            /*      Liste params :
                - url => selection
                - coloris
                - taille
                - matiere
            */

            // On met la ligne du tableau à vide si il n'est pas initialisé
        /*for (let i = 0; i <= 3; i++) {
            if (typeof(params[i]) === 'undefined') params[i] = []
        }*/

        this.moduleService.modeSaisie().then(data=>{

            let mode:string;
            if(data==="1") mode = 'ligne'
            else if(data==="2") mode = 'tableau'

                this.httpClient.post(this.httpRequest.FiltreApply2,{
                    "login":sessionStorage.getItem("loginCompte"), //les produits retournés correspondent à ceux associés au codeTarif du client
                    "mode":mode,
                    "url":params[0],
                    "coloris": params[1],
                    "taille": params[2],
                    "matiere": params[3]
                }).subscribe(data=>{
                var keys = Object.keys(data); //transforme l'objet data retourné par httpClient en un tableau
                var len = keys.length; //récupère la longueur de ce tableau
                this.produits=[]; //rénitialise le tableau produit à chaque appel de la fonction
                if(len>0){
                    for(let i=1;i<len;i++){
                        this.produits[Number(data[i].positionGalerie)-1]=new Produits(Number(data[i].idproduit),
                        data[i].codeSaison,
                        data[i].libelle,
                        data[i].refproduit, //reference du produit
                        data[i].prix, //prix
                        this.imageService.PhotosArt+"/"+data[i].imageArt, //image
                        '', //taille
                        data[i].codeColori, //coloris
                        data[i].codeMarque, //marque
                        data[i].codeTheme, //Theme
                        data[i].codeFamille, //Famille
                        data[i].codeSousFamille, //sous famille
                        data[i].codeModele, //modele
                        Number(data[i].positionGalerie),
                        data[i].promo,
                        data[i]["arrayColori"],
                        data[i].nbRef,
                        data[i].selection,
                        data[i].tarif_promo,
                        data[i].libcolori,
                        data[i].codetarif,
                        data[i].tarif_pvc,
                        data[i]["codeColoris"],
                        data[i]["imageMiniature"],
                        data[i].libelle2,
                        data[i].texteLibre,
                        data[i].champsstat,
                        data[i].imageZoom,
                        data[i].arrayTarif,
                        data[i].codeLigne,
                        data[i].libelleANG
                        );
                    }
                }
                this.emitProduits();
            });
        });
    }

    /* Pour les tailles */

    recupTailleFiltre(langue){
        if(sessionStorage.getItem("isLoggedIn")=="true"){
            this.tailleFiltre=[];
            if(sessionStorage.getItem("TailleFiltre")!= null){
              var array:any=[]
              const myArray = JSON.parse(sessionStorage.getItem("TailleFiltre"))
              myArray.forEach(element => {
                this.tailleFiltre.push(element)
              });
              this.emitTailleFiltre();
            }else{
              this.httpClient.post(this.httpRequest.InfoFiltre,{
                "login":sessionStorage.getItem("loginCompte"),
                "type":"taille",
                "langue":langue
            }).subscribe(data=>{
                for(let i=1;i<Object.keys(data).length;i++){
                    var array:any=[]
                    for(let j=0;j<data[i].taille.length;j++){
                      const monIdConstruit =j+1
                        array.push([
                            {
                                "codeGTT": data[i].codeGammeTaille+''+monIdConstruit,
                                "taille":data[i].taille[j],
                                "select":0
                            }
                        ]);
                        // array.push([
                        //     {
                        //         "taille":data[i].taille[j],
                        //         "select":0
                        //     }
                        // ]);
                    }
                    this.tailleFiltre.push(new TailleFiltre(data[i].codeGammeTaille,array));
                }
                sessionStorage.setItem("TailleFiltre", JSON.stringify(this.tailleFiltre))
              this.emitTailleFiltre();
            })
            }
        }
    }


    recupColorisFiltreBox(langue){
        if(sessionStorage.getItem("isLoggedIn")=="true"){
            this.coloriFiltreBox=[];
              if(sessionStorage.getItem("ColorisFiltre")!= null){
                const getStringItem = sessionStorage.getItem("ColorisFiltre")
                this.coloriFiltreBox.push(getStringItem.split(','))
                this.coloriFiltreBox = this.coloriFiltreBox[0]
                this.emitColorisFiltre();
              }else{
                this.httpClient.post(this.httpRequest.InfoFiltre,{
                    "login":sessionStorage.getItem("loginCompte"),
                    "type":"colori",
                    "langue":langue
                }).subscribe(data=>{
                    var len = Object.keys(data).length
                    for(let i=1;i<len;i++){
                        this.coloriFiltreBox.push(data[i]);
                    }
                    sessionStorage.setItem("ColorisFiltre", this.coloriFiltreBox.toString())
                    this.emitColorisFiltre();
                })
              }
        }
    }


    recupMatiereFiltre(langue){
        if(sessionStorage.getItem("isLoggedIn")=="true"){
            this.matiereFiltreBox=[];
            if(sessionStorage.getItem("MatiereFiltre")!= null){
              const getStringItem = sessionStorage.getItem("MatiereFiltre")
              this.matiereFiltreBox.push( getStringItem.split(','))
              this.emitMatiereFiltre();
            }else{
              this.httpClient.post(this.httpRequest.InfoFiltre,{
                  "login":sessionStorage.getItem("loginCompte"),
                  "type":"matiere",
                  "langue":langue
              }).subscribe(data=>{
                  var len = Object.keys(data).length
                  for(let i=1;i<(len+1);i++){
                      this.matiereFiltreBox.push(data[i]);
                  }
                  sessionStorage.setItem("MatiereFiltre", this.matiereFiltreBox.toString())
                  this.emitMatiereFiltre();
              })
            }
        }
    }


    recupLigneFiltre(langue){
        if(sessionStorage.getItem("isLoggedIn")=="true"){
            this.ligneFiltreBox=[];
            if(sessionStorage.getItem("LigneFiltre")!= null){
              const getStringItem = sessionStorage.getItem("LigneFiltre")
               this.ligneFiltreBox.push( getStringItem.split(','))
               this.ligneFiltreBox = this.ligneFiltreBox[0];
               this.emitLigneFiltre();
            }else{
            this.httpClient.post(this.httpRequest.InfoFiltre,{
                "login":sessionStorage.getItem("loginCompte"),
                "type":"ligne",
                "langue":langue
            }).subscribe(data=>{
                var len = Object.keys(data).length
                for(let i=1;i<(len+1);i++){
                    this.ligneFiltreBox.push(data[i]);
                }
                sessionStorage.setItem("LigneFiltre", this.ligneFiltreBox.toString())
                this.emitLigneFiltre();
            })
            }
        }
    }


    recupFamilleFiltre(langue){
        if(sessionStorage.getItem("isLoggedIn")=="true"){
            this.familleFiltreBox=[];
            if(sessionStorage.getItem("FamilleFiltre")!= null){
              const getStringItem = sessionStorage.getItem("FamilleFiltre")
               this.familleFiltreBox.push(getStringItem.split(','))
               this.familleFiltreBox = this.familleFiltreBox[0]
               this.emitFamilleFiltre();
             }else{
              this.httpClient.post(this.httpRequest.InfoFiltre,{
                  "login":sessionStorage.getItem("loginCompte"),
                  "type":"famille",
                  "langue":langue
              }).subscribe(data=>{
                  var len = Object.keys(data).length
                  for(let i=1;i<(len+1);i++){
                      this.familleFiltreBox.push(data[i]);
                  }
                sessionStorage.setItem("FamilleFiltre", this.familleFiltreBox.toString())
                  this.emitFamilleFiltre();
              })
             }
        }
    }


    recupModeleFiltre(langue){
      if(sessionStorage.getItem("isLoggedIn")=="true"){
          this.modeleFiltreBox=[];
          if(sessionStorage.getItem("ModeleFiltre") !== null) {
            const getStringItem =sessionStorage.getItem("ModeleFiltre");
            this.modeleFiltreBox.push( getStringItem.split(','))
            this.modeleFiltreBox = this.modeleFiltreBox[0]
            this.emitModeleFiltre();
          } else {
          this.httpClient.post(this.httpRequest.InfoFiltre,{
              "login":sessionStorage.getItem("loginCompte"),
              "type":"modele",
              "langue":langue
          }).subscribe(data=>{
              var len = Object.keys(data).length
              for(let i=1;i<(len+1);i++){
                  this.modeleFiltreBox.push(data[i]);
              }
              sessionStorage.setItem("ModeleFiltre", this.modeleFiltreBox.toString())
              this.emitModeleFiltre();
          })
          }
      }
  }


    recupSousFamilleFiltre(langue){
      if(sessionStorage.getItem("isLoggedIn")=="true"){
          this.sousFamilleFiltreBox=[];
          if(sessionStorage.getItem("SousFamilleFiltre") !== null) {
            const getStringItem = sessionStorage.getItem("SousFamilleFiltre");
            this.sousFamilleFiltreBox.push( getStringItem.split(','))
            this.sousFamilleFiltreBox = this.sousFamilleFiltreBox[0]
            this.emitSousFamilleFiltre();
          } else {
          this.httpClient.post(this.httpRequest.InfoFiltre,{
              "login":sessionStorage.getItem("loginCompte"),
              "type":"sous-famille",
              "langue":langue
          }).subscribe(data=>{
              var len = Object.keys(data).length
              for(let i=1;i<(len+1);i++){
                  this.sousFamilleFiltreBox.push(data[i]);
              }
              sessionStorage.setItem("SousFamilleFiltre", this.sousFamilleFiltreBox.toString())
              this.emitSousFamilleFiltre();
          })
          }
      }
  }

  recupMarqueFiltre(langue){
    if(sessionStorage.getItem("isLoggedIn")=="true"){
        this.marqueFiltreBox=[];
        if(sessionStorage.getItem("MarqueFiltre") != null){
          const getStringItem = sessionStorage.getItem("MarqueFiltre");
          this.marqueFiltreBox.push( getStringItem.split(','));
          this.emitMarqueFiltre();
        }else{
          this.httpClient.post(this.httpRequest.InfoFiltre,{
            "login":sessionStorage.getItem("loginCompte"),
            "type":"marque",
            "langue":langue
        }).subscribe(data=>{
            var len = Object.keys(data).length
            for(let i=1;i<(len+1);i++){
                this.marqueFiltreBox.push(data[i]);
            }
            sessionStorage.setItem("MarqueFiltre", this.marqueFiltreBox.toString());
            this.emitMarqueFiltre();
        })
        }

    }
}


    recupThemeFiltre(langue){
        if(sessionStorage.getItem("isLoggedIn")=="true"){
            this.themeFiltreBox=[];
            if(sessionStorage.getItem("ThemeFiltre")!= null){
              const getStringItem = sessionStorage.getItem("ThemeFiltre")
              this.themeFiltreBox.push( getStringItem.split(','))
              this.emitThemeFiltre();
            }else{
              this.httpClient.post(this.httpRequest.InfoFiltre,{
                  "login":sessionStorage.getItem("loginCompte"),
                  "type":"theme",
                  "langue":langue
              }).subscribe(data=>{
                  var len = Object.keys(data).length
                  for(let i=1;i<(len+1);i++){
                      this.themeFiltreBox.push(data[i]);
                  }
                  sessionStorage.setItem("ThemeFiltre", this.themeFiltreBox.toString())
                  this.emitThemeFiltre();
              })
            }
        }
    }


    /* Fonction pour la recherche de produits dans la galerie */
    searchProduit(value,langue){ //prend en paramètre la valeur entrée par l'utilisateur
        if(sessionStorage.getItem("isLoggedIn")=="true"){ //si l'utilisateur est connecté
            if(value===""){ //si le champ est vide recharge els produits sur la page
                this.recupProduit(langue);
                this.emitProduits();
            }else{
                this.httpClient.post(this.httpRequest.SearchProduit,{
                    "login":sessionStorage.getItem("loginCompte"),
                    "value":value
                }).subscribe(data=>{
                    var keys = Object.keys(data); //transforme l'objet data retourné par httpClient en un tableau
                    var len = keys.length; //récupère la longueur de ce tableau
                    this.produitsSearch=[]; //rénitialise le tableau produit à chaque appel de la fonction
                    if(len>0){
                        var j=0;
                        for(let i=1;i<len;i++){
                            this.produitsSearch[Number(data[i].positionGalerie)-1]=new Produits(Number(data[i].idproduit),
                            data[i].codeSaison,
                            data[i].libelle,
                            data[i].refproduit, //reference du produit
                            data[i].prix, //prix
                            this.imageService.PhotosArt+"/"+data[i].imageArt, //image
                            '', //taille
                            data[i].codeColori, //coloris
                            data[i].codeMarque, //marque
                            data[i].codeTheme, //Theme
                            data[i].codeFamille, //Famille
                            data[i].codeSousFamille, //sous famille
                            data[i].codeModele, //modele
                            Number(data[i].positionGalerie),
                            data[i].promo,
                            data[i]["arrayColori"],
                            data[i].nbRef,
                            data[i].selection,
                            data[i].tarif_promo,
                            data[i].libcolori,
                            data[i].codetarif,
                            data[i].tarif_pvc,
                            data[i]["codeColoris"],
                            data[i]["imageMiniature"],
                            data[i].libelle2,
                            data[i].texteLibre,
                            data[i].champsstat,
                            data[i].imageZoom,
                            data[i].arrayTarif,
                            data[i].codeLigne,
                            data[i].libelleANG
                            );
                            j++;
                        }
                    }
                    this.emitProduits();
                })
            }
        }
        this.moduleService.visGalerieStatus().then(
            (status)=>{
                if(status){
                    if(!(sessionStorage.getItem("isLoggedIn")=='true')){
                        if(value===""){ //si le champ est vide recharge els produits sur la page
                            this.produits=[];
                        }else{
                            this.httpClient.post(this.httpRequest.SearchProduit,{
                                "value":value,
                                "visGalerie":"true"
                            }).subscribe(data=>{
                                var keys = Object.keys(data); //transforme l'objet data retourné par httpClient en un tableau
                                var len = keys.length; //récupère la longueur de ce tableau
                                this.produits=[]; //rénitialise le tableau produit à chaque appel de la fonction
                                if(len>0){
                                    var j=0;
                                    for(let i=1;i<len;i++){
                                        this.produitsSearch[Number(data[i].positionGalerie)-1]=new Produits(Number(data[i].idproduit),
                                        data[i].codeSaison,
                                        data[i].libelle,
                                        data[i].refproduit, //reference du produit
                                        data[i].prix, //prix
                                        this.imageService.PhotosArt+"/"+data[i].imageArt, //image
                                        '', //taille
                                        data[i].codeColori, //coloris
                                        data[i].codeMarque, //marque
                                        data[i].codeTheme, //Theme
                                        data[i].codeFamille, //Famille
                                        data[i].codeSousFamille, //sous famille
                                        data[i].codeModele, //modele
                                        Number(data[i].positionGalerie),
                                        data[i].promo,
                                        data[i]["arrayColori"],
                                        data[i].nbRef,
                                        data[i].selection,
                                        data[i].tarif_promo,
                                        data[i].libcolori,
                                        data[i].codetarif,
                                        data[i].tarif_pvc,
                                        data[i]["codeColoris"],
                                        data[i]["imageMiniature"],
                                        data[i].libelle2,
                                        data[i].texteLibre,
                                        data[i].champsstat,
                                        data[i].imageZoom,
                                        data[i].arrayTarif,
                                        data[i].codeLigne,
                                        data[i].libelleANG
                                        );
                                        j++;
                                    }
                                }
                                this.emitProduits();
                            })
                        }
                    }
                }
            }
        )
    }

    emitColori(){
        this.coloriFiltreSubject.next(this.coloriFiltre);
    }

    emitTailleFiltre(){
        this.tailleFiltreSubject.next(this.tailleFiltre);
    }

    emitColorisFiltre(){
        this.colorisFiltreBoxSubject.next(this.coloriFiltreBox);
    }

    emitMatiereFiltre(){
        this.matiereFiltreBoxSubject.next(this.matiereFiltreBox);
    }

    emitLigneFiltre(){
        this.ligneFiltreBoxSubject.next(this.ligneFiltreBox);
    }

    emitFamilleFiltre(){
        this.familleFiltreBoxSubject.next(this.familleFiltreBox);
    }

    emitThemeFiltre(){
        this.themeFiltreBoxSubject.next(this.themeFiltreBox);
    }

    emitMarqueFiltre(){
      this.marqueFiltreBoxSubject.next(this.marqueFiltreBox)
    }

    emitSousFamilleFiltre(){
      this.sousFamilleFiltreBoxSubject.next(this.sousFamilleFiltreBox)
    }

    emitModeleFiltre(){
      this.modeleFiltreBoxSubject.next(this.modeleFiltreBox)
    }

    //Méthode pour connaitre le type de périphérique utilisé
    detectMobile(){
        const deviceInfo= this.detectService.getDeviceInfo();
        this.isMobile = this.detectService.isMobile();
        this.isTablet = this.detectService.isTablet();
        this.isDesktop = this.detectService.isDesktop();
    }
}
