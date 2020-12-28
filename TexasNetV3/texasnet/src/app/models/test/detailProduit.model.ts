export class DetailProduit{
    constructor(
      public id:number,
      public saison:string,
      public libelle:string,
      public refproduit:string,
      public prix:number,
      public image:string,
      public taille:string,
      public coloris:string,
      public marque:string,
      public theme:string,
      public famille:string,
      public sousFamille:string,
      public modele:string,
      public position:any,
      public promo:number,
      public arrayColori:any[],
      public nbColori:number,
      public selection:number,
      public tarifPromo:number,
      public libcolori:string,
      public codetarif:string,
      public tarifpvc:number,
      public codeColoris:any[],
      public imageMiniature:any[],
      public libelle2:string,
      public texteLibre:string,
      public imageZoom:string,
      public arrayTarif:any[],
      public ligne:string,
      public a_position:any[],
      public a_images:any[],    // Contient les images normals, zooms et miniatures
      public a_tailles:any[],
      public a_coloris:any[],   // Contient les codes et les libelles
      public a_promotion:any[]
    ){}
}
