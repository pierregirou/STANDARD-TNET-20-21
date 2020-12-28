import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { ContenuAccueilComponent } from './contenu-accueil.component';

describe('ContenuAccueilComponent', () => {
  let component: ContenuAccueilComponent;
  let fixture: ComponentFixture<ContenuAccueilComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ ContenuAccueilComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(ContenuAccueilComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
