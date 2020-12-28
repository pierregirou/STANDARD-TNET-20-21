import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { FiltreBoxComponent } from './filtre-box.component';

describe('FiltreBoxComponent', () => {
  let component: FiltreBoxComponent;
  let fixture: ComponentFixture<FiltreBoxComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ FiltreBoxComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(FiltreBoxComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
