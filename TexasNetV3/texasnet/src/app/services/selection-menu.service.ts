import { Injectable } from '@angular/core';
import { Subject } from 'rxjs';
import { HttpClient } from '@angular/common/http';
import { HttpRequest } from '../services/http-request.service';
import { ImageService } from '../services/images.service';
import { isDefined } from '@angular/compiler/src/util';
import { Produits } from '../models/produits.model';
import { Colori } from '../models/coloris.models';

@Injectable()
export class SelectionMenuService{
    public produits:Produits[]=[];
    colorInfo:any[];
    produitSubject=new Subject<Produits[]>();
    coloriFiltre2:Colori[]=[];
    coloriFiltreSubject2=new Subject<Colori[]>();
    codeGamme:any[]=[];
    codeGammeSubject=new Subject<any[]>();
    constructor(private imageService:ImageService,private httpClient:HttpClient,private httpRequest:HttpRequest){}

    emitProduits(){
        this.produitSubject.next(this.produits.slice());
    }

    getSelectionProduits(url:string,langue){
        this.produits=[];
        return new Promise(
            (resolve,reject)=>{
                this.httpClient.post(this.httpRequest.FiltreSelectionMenu,{
                    "login":sessionStorage.getItem("loginCompte"),
                    "url":url,
                    "arrayPtr":this.colorInfo,
                    "typeSelect":"produits",
                    "langue":langue
                }).subscribe(data=>{
                    if(isDefined(data[6])){
                        var keys = Object.keys(data[6]); //transforme l'objet data retourné par httpClient en un tableau
                        var len = keys.length; //récupère la longueur de ce tableau
                        this.produits=[]; //rénitialise le tableau produit à chaque appel de la fonction
                        if(len>0){
                            var j=0;

                            for(let i=1;i<len+1;i++){
                                this.produits[Number(data[6][i].positionGalerie)-1]=new Produits(Number(data[6][i].idproduit),
                                data[6][i].codeSaison,
                                data[6][i].libelle,
                                data[6][i].refproduit, //reference du produit
                                data[6][i].prix, //prix
                                this.imageService.PhotosArt+"/"+data[6][i].imageArt, //image
                                '', //taille
                                data[6][i].codeColori, //coloris
                                data[6][i].codeMarque, //marque
                                data[6][i].codeTheme, //Theme
                                data[6][i].codeFamille, //Famille
                                data[6][i].codeSousFamille, //sous famille
                                data[6][i].codeModele, //modele
                                Number(data[6][i].positionGalerie),
                                data[6][i].promo,
                                data[6][i]["arrayColori"],
                                data[6][i].nbRef,
                                data[6][i].selection,
                                data[6][i].tarif_promo,
                                data[6][i].libcolori,
                                data[6][i].codetarif,
                                data[6][i].tarif_pvc,
                                data[6][i]["codeColoris"],
                                data[6][i]["imageMiniature"],
                                data[6][i].libelle2,
                                data[6][i].texteLibre,
                                data[6][i].champsstat,
                                data[6][i].imageZoom,
                                data[6][i].arrayTarif,
                                data[6][i].codeLigne,
                                data[6][i].libelleANG
                                );
                                j++;
                            }
                        }
                    }else{
                        this.produits=[];
                    }
                    this.recupColoriFiltre2();
                    resolve(this.produits);
                });
            }
        )
    }

        /* Fonction pour la recherche de produits dans la galerie */
        searchProduit(value,url,langue){ //prend en paramètre la valeur entrée par l'utilisateur
            if(value===""){ //si le champ est vide recharge els produits sur la page
                this.getSelectionProduits(url,langue);
                this.emitProduits();
            }else{
                this.httpClient.post(this.httpRequest.SearchProduit,{
                    "login":sessionStorage.getItem("loginCompte"),
                    "url":url,
                    "typeSelect":"produits",
                    "value":value
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

    getSelectionPromo(url:string,langue){
        this.produits=[];
        return new Promise(
            (resolve,reject)=>{
                this.httpClient.post(this.httpRequest.FiltreSelectionMenu,{
                    "login":sessionStorage.getItem("loginCompte"),
                    "url":url,
                    "typeSelect":"promo",
                    "langue":langue
                }).subscribe(data=>{
                    if(isDefined(data[6])){
                        var keys = Object.keys(data[6]); //transforme l'objet data retourné par httpClient en un tableau
                        var len = keys.length; //récupère la longueur de ce tableau
                        this.produits=[]; //rénitialise le tableau produit à chaque appel de la fonction
                        if(len>0){
                            var j=0;
                            for(let i=1;i<len+1;i++){
                                this.produits[Number(data[6][i].positionGalerie)-1]=new Produits(Number(data[6][i].idproduit),
                                data[6][i].codeSaison,
                                data[6][i].libelle,
                                data[6][i].refproduit, //reference du produit
                                data[6][i].prix, //prix
                                this.imageService.PhotosArt+"/"+data[6][i].imageArt, //image
                                '', //taille
                                data[6][i].codeColori, //coloris
                                data[6][i].codeMarque, //marque
                                data[6][i].codeTheme, //Theme
                                data[6][i].codeFamille, //Famille
                                data[6][i].codeSousFamille, //sous famille
                                data[6][i].codeModele, //modele
                                Number(data[6][i].positionGalerie),
                                data[6][i].promo,
                                data[6][i]["arrayColori"],
                                data[6][i].nbRef,
                                data[6][i].selection,
                                data[6][i].tarif_promo,
                                data[6][i].libcolori,
                                data[6][i].codetarif,
                                data[6][i].tarif_pvc,
                                data[6][i]["codeColoris"],
                                data[6][i]["imageMiniature"],
                                data[6][i].libelle2,
                                data[6][i].texteLibre,
                                data[6][i].champsstat,
                                data[6][i].imageZoom,
                                data[6][i].arrayTarif,
                                data[6][i].codeLigne,
                                data[6][i].libelleANG
                                );
                                j++;
                            }
                        }
                    }else{
                        this.produits=[];
                    }
                    resolve(this.produits);
                });
            }
        )
    }

    recupColoriFiltre2(){
      let colorPresent = [];
      this.coloriFiltre2=[]; // rénitialise l'array es coloris
      for(let unProduit of this.produits) {
        for(let uneCouleur of unProduit.arrayColori) {
          if(colorPresent.indexOf(uneCouleur.libcolori) === -1) {
              colorPresent.push(uneCouleur.libcolori);
          }
        }
      }
      colorPresent.sort();
      for(let uneCouleur2 of colorPresent) {
        this.coloriFiltre2.push(new Colori(uneCouleur2,'red'));
      }
      this.emitColori();
    }

    recupGammeFiltre(){
      this.codeGamme = [];
/*     for(let unProduit of this.produits) {
        if(this.codeGamme.indexOf(unProduit.codeGammeTaille) === -1) {
            this.codeGamme.push(unProduit.codeGammeTaille);
        }
      }
      this.emitCodeGamme();*/
    }

    emitColori(){
        this.coloriFiltreSubject2.next(this.coloriFiltre2);
    }

    emitCodeGamme(){
        this.codeGammeSubject.next(this.codeGamme);
    }

}
