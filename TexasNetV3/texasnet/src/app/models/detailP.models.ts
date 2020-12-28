export class Detail{
    constructor(
        public taille:string,
        public quantite:number,
        public idproduit:number,
        public prix:number,
        public value:string,
        public select:number, //permet de connaitre les tailles sélectionnées par le client
        public quantiteTaille:number, //permet de connaître la quantité de produit sélectionnée par le client
        public coloris:string,
        public prixPromo:number,
        public codeColoris:string
    ){}
}